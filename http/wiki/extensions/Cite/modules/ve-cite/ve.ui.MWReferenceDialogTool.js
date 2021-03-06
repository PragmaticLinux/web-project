/*!
 * VisualEditor MediaWiki Reference dialog tool classes.
 *
 * @copyright 2011-2016 Cite VisualEditor Team and others; see AUTHORS.txt
 * @license The MIT License (MIT); see LICENSE.txt
 */

/**
 * MediaWiki UserInterface reference tool.
 *
 * @class
 * @extends ve.ui.FragmentWindowTool
 * @constructor
 * @param {OO.ui.ToolGroup} toolGroup
 * @param {Object} [config] Configuration options
 */
ve.ui.MWReferenceDialogTool = function VeUiMWReferenceDialogTool() {
	ve.ui.MWReferenceDialogTool.super.apply( this, arguments );
};
OO.inheritClass( ve.ui.MWReferenceDialogTool, ve.ui.FragmentWindowTool );
ve.ui.MWReferenceDialogTool.static.name = 'reference';
ve.ui.MWReferenceDialogTool.static.group = 'object';
ve.ui.MWReferenceDialogTool.static.icon = 'reference';
ve.ui.MWReferenceDialogTool.static.title =
	OO.ui.deferMsg( 'cite-ve-dialogbutton-reference-tooltip' );
ve.ui.MWReferenceDialogTool.static.modelClasses = [ ve.dm.MWReferenceNode ];
ve.ui.MWReferenceDialogTool.static.commandName = 'reference';
ve.ui.MWReferenceDialogTool.static.autoAddToCatchall = false;
ve.ui.toolFactory.register( ve.ui.MWReferenceDialogTool );

/**
 * MediaWiki UserInterface use existing reference tool.
 *
 * @class
 * @extends ve.ui.WindowTool
 * @constructor
 * @param {OO.ui.ToolGroup} toolGroup
 * @param {Object} [config] Configuration options
 */
ve.ui.MWUseExistingReferenceDialogTool = function VeUiMWUseExistingReferenceDialogTool() {
	ve.ui.MWUseExistingReferenceDialogTool.super.apply( this, arguments );
};
OO.inheritClass( ve.ui.MWUseExistingReferenceDialogTool, ve.ui.WindowTool );
ve.ui.MWUseExistingReferenceDialogTool.static.name = 'reference/existing';
ve.ui.MWUseExistingReferenceDialogTool.static.group = 'object';
ve.ui.MWUseExistingReferenceDialogTool.static.icon = 'reference-existing';
ve.ui.MWUseExistingReferenceDialogTool.static.title =
	OO.ui.deferMsg( 'cite-ve-dialog-reference-useexisting-tool' );
ve.ui.MWUseExistingReferenceDialogTool.static.commandName = 'reference/existing';
ve.ui.MWUseExistingReferenceDialogTool.static.autoAddToGroup = false;
ve.ui.MWUseExistingReferenceDialogTool.static.autoAddToCatchall = false;
ve.ui.toolFactory.register( ve.ui.MWUseExistingReferenceDialogTool );

ve.ui.commandRegistry.register(
	new ve.ui.Command(
		'reference', 'window', 'open',
		{ args: [ 'reference' ], supportedSelections: [ 'linear' ] }
	)
);

/* If Citoid is installed these will be overridden */
ve.ui.sequenceRegistry.register(
	new ve.ui.Sequence( 'wikitextRef', 'reference', '<ref', 4 )
);

ve.ui.commandHelpRegistry.register( 'insert', 'ref', {
	sequences: [ 'wikitextRef' ],
	label: OO.ui.deferMsg( 'cite-ve-dialog-reference-title' )
} );

ve.ui.mwWikitextTransferRegistry.register( 'reference', /<ref[^>]*>/ );

/**
 * MediaWiki UserInterface references list tool.
 *
 * @class
 * @extends ve.ui.FragmentWindowTool
 * @constructor
 * @param {OO.ui.ToolGroup} toolGroup
 * @param {Object} [config] Configuration options
 */
ve.ui.MWReferencesListDialogTool = function VeUiMWReferencesListDialogTool() {
	ve.ui.MWReferencesListDialogTool.super.apply( this, arguments );
};
OO.inheritClass( ve.ui.MWReferencesListDialogTool, ve.ui.FragmentWindowTool );
ve.ui.MWReferencesListDialogTool.static.name = 'referencesList';
ve.ui.MWReferencesListDialogTool.static.group = 'object';
ve.ui.MWReferencesListDialogTool.static.icon = 'references';
ve.ui.MWReferencesListDialogTool.static.title =
	OO.ui.deferMsg( 'cite-ve-dialogbutton-referenceslist-tooltip' );
ve.ui.MWReferencesListDialogTool.static.modelClasses = [ ve.dm.MWReferencesListNode ];
ve.ui.MWReferencesListDialogTool.static.commandName = 'referencesList';
ve.ui.toolFactory.register( ve.ui.MWReferencesListDialogTool );
