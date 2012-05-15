var App = App || { };

var a = [];

App.validate = function(form) {
    $('#errors').html('');
    var formName = (form.getAttribute('name')) ? form.getAttribute('name') : 'form';
    var rules = Forms[formName];
    var formValidates = true;

    for(var key in rules) {
	var element = form[key];
	var ruleset = rules[key];

	var value = (element.nodeName == 'INPUT') ? element.value : element.innerHTML;

	for(var i in ruleset) {
	    var validatorName = ruleset[i].name;
	    var elementValidates = App.Validator[validatorName](value, ruleset[i].parameters);

	    if(elementValidates) {
		//You can replace this with whatever you want to happen to invalid fields.
		$(element).removeClass('invalid');
	    } else {
		//Again, replace if you aren't using YUI.
		$(element).addClass('invalid');
		$('#errors').append(element.name + ' is incorrect<br/>');

		//but not this
		formValidates = false;
		break;
	    }
	}
    }
    if (!formValidates) {
	$('#errors').show(200);
    }

    return formValidates;
}


App.Validator = {
    Zend_Validate_NotEmpty: function(value, parameters) {
	if(value != '')
	    return true;

	return false;
    },

    Zend_Validate_Alnum: function(value, parameters) {
	if(parameters.allowWhiteSpace)
	    return value.match(/^[a-z0-9\s]*$/i);
	else
	    return value.match(/^[a-z0-9]*$/i);
    },

    Zend_Validate_Digits: function(value, parameters) {
	if(parameters.allowWhiteSpace) {
	    return value.match(/^[0-9\s]+$/i);
	} else {
	    return value.match(/^[0-9]+$/i);
	}
    },

    Application_Form_Validator_Duration: function(value, parameters) {
	if (!value.match(/^[0-9]+$/)) {
	    return value.match(/^[0-9]+(\.|:)[0-9]+$/i);
	}
	return true;
    }
};