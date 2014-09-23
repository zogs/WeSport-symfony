/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = [
		['Source','-','Save'],'-',['Undo','Redo'],'-',['Scayt'],'-',['NumberedList','BulletedList','Table'],'-',['Blockquote','CreateDiv','SpecialChar','Iframe'],
		'/',
		['Styles'],['Format'],'-',['Bold','Italic','Underline','Strike','TextColor'],'-',['Link','Unlink','Image','Flash','Smiley'],'-',['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		'/',
		['oembed','MediaEmbed']
	];

	config.baseUrl = "/web/";
	//config.baseDir = "D:\wamp\www\weSport\webroot";
	config.enterMod = 'ENTER_BR';

    config.filebrowserBrowseUrl = '/web/bundles/myutils/ckeditor_filemanager/index.html';

};
