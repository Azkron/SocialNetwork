/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 var errors = new Array();



function checkField(field, regs)
{
    var pass = true;
    var val = parse(field.value);
    for(var i = 0; i < regs.length; ++i)
        if(!regs[i].reg.test(val))
        {
            pass = false;
            addError(regs[i].msg);
        }
        else
            eraseError(regs[i].msg);
   
        
    return pass; 
    // I cant use errors.concat(newErrors) because that replaces the reference 
    // of the REFERENCE ARGUMENT errors to a new array, but the originally referenced array remains unchanged
}


function displayErrors(errors) {
    
        var errorDiv = document.getElementById("errors");
        if(errorDiv === null)
        {
            alert("There is no markup with id='errors' in this page");
            return false;
        }
        else
        {
            errorDiv.innerHTML = "";
            if(errors.length > 0)
            {
                var html = "<p>Please correct the following error(s) :</p>";
                    html += "<ul>";
                        for (var i in errors)
                            html += "<li>"+errors[i]+"</li>";
                    html += "</ul>";

                errorDiv.innerHTML = html;
                return true;
            }
            else
                return false;
        }
}

function eraseError(errorMsg)
{
    var index = errors.indexOf(errorMsg);
    if(index != -1)
        errors.splice(0, 1);
}

function addError(errorMsg)
{
    var index = errors.indexOf(errorMsg);
    if(index == -1)
        errors.push(errorMsg);
}

function parse(val)
{
    if(typeof val == 'number')
        return parseInt(val);
    else if(typeof val == 'string')
        return val;
    else
        alert("In parse(val) : Type not recognized!");
}