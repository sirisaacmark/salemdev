<?php 
/**
	Admin Page Framework v3.8.15 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/wp-favorite-posts>
	Copyright (c) 2013-2017, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class WPFavoritePostsAdminPageFramework_FieldType__nested extends WPFavoritePostsAdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('_nested');
    protected $aDefaultKeys = array();
    protected function getStyles() {
        return ".wp-favorite-posts-fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field.with-nested-fields > .wp-favorite-posts-fieldset.multiple-nesting {margin-left: 2em;}.wp-favorite-posts-fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field.with-nested-fields > .wp-favorite-posts-fieldset {margin-bottom: 1em;}.with-nested-fields > .wp-favorite-posts-fieldset.child-fieldset > .wp-favorite-posts-child-field-title {display: inline-block;padding: 0 0 0.4em 0;}.wp-favorite-posts-fieldset.child-fieldset > label.wp-favorite-posts-child-field-title {display: table-row; white-space: nowrap;}";
    }
    protected function getField($aField) {
        $_oCallerForm = $aField['_caller_object'];
        $_aInlineMixedOutput = array();
        foreach ($this->getAsArray($aField['content']) as $_aChildFieldset) {
            if (is_scalar($_aChildFieldset)) {
                continue;
            }
            if (!$this->isNormalPlacement($_aChildFieldset)) {
                continue;
            }
            $_aChildFieldset = $this->getFieldsetReformattedBySubFieldIndex($_aChildFieldset, ( integer )$aField['_index'], $aField['_is_multiple_fields'], $aField);
            $_oFieldset = new WPFavoritePostsAdminPageFramework_Form_View___Fieldset($_aChildFieldset, $_oCallerForm->aSavedData, $_oCallerForm->getFieldErrors(), $_oCallerForm->aFieldTypeDefinitions, $_oCallerForm->oMsg, $_oCallerForm->aCallbacks);
            $_aInlineMixedOutput[] = $_oFieldset->get();
        }
        return implode('', $_aInlineMixedOutput);
    }
}
class WPFavoritePostsAdminPageFramework_FieldType_inline_mixed extends WPFavoritePostsAdminPageFramework_FieldType__nested {
    public $aFieldTypeSlugs = array('inline_mixed');
    protected $aDefaultKeys = array('label_min_width' => '', 'show_debug_info' => false,);
    protected function getStyles() {
        return ".wp-favorite-posts-field-inline_mixed {width: 98%;}.wp-favorite-posts-field-inline_mixed > fieldset {display: inline-block;overflow-x: visible;padding-right: 0.4em;}.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields{display: inline;width: auto;table-layout: auto;margin: 0;padding: 0;vertical-align: middle;white-space: nowrap;}.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field {float: none;clear: none;width: 100%;display: inline-block;margin-right: auto;vertical-align: middle; }.with-mixed-fields > fieldset > label {width: auto;padding: 0;}.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field .wp-favorite-posts-input-label-string {padding: 0;}.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field > .wp-favorite-posts-input-label-container,.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field > * > .wp-favorite-posts-input-label-container{padding: 0;display: inline-block;width: 100%;}.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field > .wp-favorite-posts-input-label-container > label,.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field > * > .wp-favorite-posts-input-label-container > label{display: inline-block;}.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field > .wp-favorite-posts-input-label-container > label > input,.wp-favorite-posts-field-inline_mixed > fieldset > .wp-favorite-posts-fields > .wp-favorite-posts-field > * > .wp-favorite-posts-input-label-container > label > input{display: inline-block;min-width: 100%;margin-right: auto;margin-left: auto;}.wp-favorite-posts-field-inline_mixed .wp-favorite-posts-input-label-container,.wp-favorite-posts-field-inline_mixed .wp-favorite-posts-input-label-string{min-width: 0;}";
    }
}
