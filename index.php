<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="favicon.png" type="image/x-icon">
  <title>Multiple Units Converter</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center mt-5 mb-5"> 
                    <h1>Please select the unit you need to convert</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12"> 
                    <div class="form-group">
                        <label>Unit Type: </label>
                        <select id="unitTypeSelect" onchange="getUnits()" class="form-control"></select>
                    </div>
                </div>
                <div class="col-sm-4"> 
                    <div class="form-group">
                        <label>From: </label>
                        <select  id="unitSelectFrom" class="form-control"></select>
                    </div>
                </div>
                <div class="col-sm-4"> 
                    <div class="form-group">
                        <label>To: </label>
                        <select  id="unitSelectTo"class="form-control"></select>
                    </div>
                </div>
                <div class="col-sm-4"> 
                    <div class="form-group">
                        <label>Value: </label>
                        <input id="valueToConvert" class="form-control"  type="number" onkeypress="return onlynumber();"> 
                    </div>
                </div>
                <div class="col-sm-12 text-center"> 
                    <div class="form-group">
                        <button id="btnConvert" class="btn btn-primary btn-lg" onclick="convertAction()">Convert</button>
                    </div>
                </div>
                <div class="col-sm-12 text-center"> 
                    <div class="form-group">
                        <h2 id="resultValue"></h2>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html> 
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    /**@abstract\
     * call for the options needed after the page is loaded
     */
    $(document).ready(function() {
        getUnitTypes(); 
    });

     /**@abstract
     * Call for the REST API to get all the possible unit types, all the information avaliable on the form, is based on the options received by the endpoint
     * 
     * right now it is calling the open URL hosted on herokuapp
     * but it can be called by any other API
     */
    function getUnitTypes(){
        let paramURL = "https://pesslexercise.herokuapp.com/index.php/unittype/list";
        $.ajax({
            url: paramURL,
            type: "GET",
            crossDomain: true,
            dataType: "json"
        }).done(function(response) {
            let select_option = '';
            
            $.each(response, function (key, item) {  
                select_option += "<option value='"+ key +"'>"+ item +"</option>"; 
            });

            $('#unitTypeSelect').append(select_option);  
            getUnits();

        }).fail(function(jqXHR, textStatus ) {
            console.log("Request failed: " + textStatus);
        });  
    }

    /**@abstract
     * Call for the REST API to get all the avaliable options based on the unit Type selected by the user
     * 
     * right now it is calling the open URL hosted on herokuapp
     * but it can be called by any other API
     */
    function getUnits(){
        let paramURL = "https://pesslexercise.herokuapp.com/index.php/unit/list?type="+$('#unitTypeSelect').val();
        $.ajax({
            url: paramURL,
            type: "GET",
            crossDomain: true,
            dataType: "json"
        }).done(function(response) {
            let select_option = '';
            
            $.each(response, function (key, item) {  
                select_option += "<option value='"+ key +"'>"+ item +"</option>"; 
            });
            $('#unitSelectFrom').find("option").remove();
            $('#unitSelectFrom').append(select_option);  

            $('#unitSelectTo').find("option").remove();
            $('#unitSelectTo').append(select_option);  

        }).fail(function(jqXHR, textStatus ) {
            console.log("Request failed: " + textStatus);
        });
    }

    /**@abstract
     * Call for the REST API sendind the fields selected by the user
     * 
     * right now it is calling the open URL hosted on herokuapp
     * but it can be called by any other API
     */
    function convertAction(){
        if(validRequest()){
            let convertType = $('#unitTypeSelect').val();
            let urlCall = "https://pesslexercise.herokuapp.com/index.php/unit/";
            switch (convertType) {
                case 'len':
                    urlCall += "convertlength";
                    break;
                case 'temp':
                    urlCall += "convertemperature";
                    break;
                case 'speed':
                    urlCall += "convertspeed";
                    break;
                default:
                    console.log(`Sorry, we are out of ${expr}.`);
            }

            let dataValue = {from: $('#unitSelectFrom').val(), to: $('#unitSelectTo').val(), value: $.trim($('#valueToConvert').val())};
            $.ajax({
                url: urlCall,
                type: "POST",
                crossDomain: true,
                data: dataValue,
                dataType: "json"
            }).done(function(response) {
                let textReturn = '';

                if(typeof(response.error) != "undefined" && response.error !== null)
                {
                    textReturn = response.error;
                } else {
                    textReturn = response;
                }

                $('#resultValue').text(textReturn);

            }).fail(function(jqXHR, textStatus ) {
                console.log("Request failed: " + textStatus);
            });
        } else {
            $('#resultValue').text('Invalid Input Values');
        }

    }

    /**@abstract
     * Validate if the form is filled correctly
     */
    function validRequest(){
        let valueToConvert = $.trim($('#valueToConvert').val());

        if(valueToConvert == '')
        {
            return false;
        }

        if(!$.isNumeric(valueToConvert))
        {
            return false;
        }
        
        return true;
    }

    /**@abstract
     * Validate the input field so it can only allow numbers
     */
    function onlynumber(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode( key );
        var regex = /^[0-9.]+$/;
        if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }
</script>