var CRYPTOR_YFORM = (function(){
    
    /**
     * Initializer
     * @returns {void}
     */
    var _init = function() {
        _initYformManager();
    };
    
    /**
     * Initialize the yForm manager
     * @returns {void}
     */
    var _initYformManager = function() {
        var tableName = _getCurrentYformTableName();
        if (!tableName) {
            return;
        }
        var data = {
            cryptor_yform: 'field_names',
            cryptor_yform_value: tableName
        };
        var callback = function(data) {
            if (data.length) {
                var $form = $('#rex-page-yform-manager-data-edit #rex-yform > form');
                
                // Loop over each field and add en/decrypt buttons
                $.each(data, function() {
                    $('#rex-page-yform-manager-data-edit .table td[data-title="' + this.label + '"]').each(function(){
                        _initYformList($(this));
                    });
                    
                    if (_getYformFunc($form) === 'edit') {
                        $('#rex-page-yform-manager-data-edit #yform-formular-' + this.name).each(function(){
                            _initYformElement($(this));
                        });
                    }
                });
                
                // Add events to form
                _initYformSubmit($form); 
            }
        };
        _ajaxCallback(data, callback);
    };
    
    /**
     * Encrypt unencrypted fields onSubmit
     * @returns {void}
     */
    var _initYformSubmit = function($form) {
        var submitForm = function() {
            $form.off().submit();
        };
        $form.on({
            submit: function(e){
                e.preventDefault();
                
                // Collect each unencrypted value
                var values = [];
                var $elements = $('#rex-page-yform-manager-data-edit .cryptor-yform-element').not('.is-encrypted');
                $elements.each(function(){
                    values.push($(this).find('.form-control').val());
                });
                
                // Nothing to encrypt, submit
                if (!values.length) {
                    submitForm();
                }
                
                // Initiate encryption and submit the form
                var data = {
                    cryptor_yform: 'backend_encrypt',
                    cryptor_yform_value: values
                };
                var callback = function(data) {
                    $.each(data, function(index) {
                        $elements.eq(index).find('.form-control').val(this);
                    });
                    submitForm();
                };
                _ajaxCallback(data, callback);
            }
        });
    };

    /**
     * Event: add decrypt button to yform list items
     * @param {obj} $tableCell
     * @returns {void}
     */
    var _initYformList = function($tableCell) {
        var $el = _getYformListElement($tableCell);
        if (!$el.text()) {
            return;
        }
        var $button = $('<span class="fa cryptor-yform-decrypt-icon"></span>')
            .click(function(e){
                e.preventDefault();
                var method = $tableCell.hasClass('is-encrypted') ? 'backend_decrypt' : 'backend_encrypt';
                var data = {
                    cryptor_yform: method,
                    cryptor_yform_value: $el.attr('title')
                };
                var callback = function(data) {
                    $el.attr('title', data);
                    $el.text(data);
                    $tableCell.toggleClass('is-encrypted');
                };
                _ajaxCallback(data, callback);
            });
        $tableCell.addClass('cryptor-yform-element is-encrypted');
        $el.before($button);
    };

    /**
     * Event: add decrypt button to yform form elements
     * @param {obj} $formElement
     * @returns {void}
     */
    var _initYformElement = function($formElement) {
        
        var $el = _getYformElement($formElement);
        if (!$el) {
            return;
        }
        var $button = $('<span class="fa cryptor-yform-decrypt-icon"></span>')
            .click(function(e){
                e.preventDefault();
                var method = $formElement.hasClass('is-encrypted') ? 'backend_decrypt' : 'backend_encrypt';
                var data = {
                    cryptor_yform: method,
                    cryptor_yform_value: $el.val()
                };
                var callback = function(data) {
                    $formElement.toggleClass('is-encrypted');
                    $el.val(data);
                };
                _ajaxCallback(data, callback);
            });
        $formElement.addClass('cryptor-yform-element is-encrypted');
        $button.appendTo($formElement.find('.control-label'));
    };
    
    /**
     * Ajax: Post request to redaxo backend
     * @param {obj} data
     * @param {func} callback
     * @returns {callback}
     */
    var _ajaxCallback = function(data, callback) {
        $.ajax({
            dataType: 'json',
            url: '/redaxo/',
            method: 'POST',
            data: data,
            success: function(data) {
                callback(data);
            }
        });
    };
    
    /**
     * Returns the current tablename from yform header element
     * @returns {string} tableName
     */
    var _getCurrentYformTableName = function() {
        var tableName = $('#rex-page-yform-manager-data-edit .page-header h1 small').text();
        return tableName.replace(/\[|\]/gi, '');
    };
    
    /**
     * Returns the value of the func field
     * @param {obj} $form
     * @returns {mixed} func on success | null
     */
    var _getYformFunc = function($form) {
        return $form.find('input[name="func"]').val();
    };
    
    /**
     * Extract the form control element
     * @param {obj} $formElement
     * @returns {mixed} $element on success | null
     */
    var _getYformElement = function($formElement){
        var $element = $formElement.find('.form-control');
        if ($element.prop('type') === 'text') {
            return $element;
        }
        else if ($element.prop('type') === 'email') {
            $element.prop('type', 'text');
            return $element;
        }
        else if ($element.prop('tagName').toLowerCase() === 'textarea') {
            return $element;
        }
        return null;
    };
    
    /**
     * Returns list element, and wrap it with a span if needed
     * @param {obj} $el
     * @returns {obj} $span
     */
    var _getYformListElement = function($el) {
        var $span = $el.find('span');
        if (!$span.length) {
            var content = $el.html();
            $el.wrapInner('<span></span>');
            $span = $el.find('span');
            $span.attr('title', content);
        }
        return $span.addClass('cryptor-yform-list-value-wrapper');
    };
    
    /**
     * Return an interface
     * @returns {object}
     */
    return {
        init: function() {
            _init();
        }
    };
})();

$(document).ready(function(){
    CRYPTOR_YFORM.init();
});