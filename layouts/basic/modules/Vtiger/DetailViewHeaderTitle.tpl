{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
	<div class="col-xs-10 col-sm-9 col-md-4 margin0px">
		<div class="moduleIcon">
			<span class="detailViewIcon userIcon-{$MODULE}"></span>
		</div>
		<div class="paddingLeft5px pull-left">
			<h4 class="paddingLeft5px recordLabel textOverflowEllipsis pushDown marginbottomZero" title="{$RECORD->getName()}">
				{foreach item=NAME_FIELD from=$MODULE_MODEL->getNameFields()}
					{assign var=FIELD_MODEL value=$MODULE_MODEL->getField($NAME_FIELD)}
						{if $FIELD_MODEL->getPermissions()}
							<span class="moduleColor_{$MODULE_NAME} {$NAME_FIELD}">{$RECORD->getDisplayValue($NAME_FIELD)}</span>&nbsp;
						{/if}
				{/foreach}
			</h4>
			{if $MODULE_NAME}
				<div class="paddingLeft5px">
					<span class="muted">
						{vtranslate('Assigned To',$MODULE_NAME)}: {$RECORD->getDisplayValue('assigned_user_id')}
						{assign var=SHOWNERS value=$RECORD->getDisplayValue('shownerid')}
						{if $SHOWNERS != ''}
						<br/>{vtranslate('Share with users',$MODULE_NAME)} {$SHOWNERS}
						{/if}
					</span>
				</div>
			{/if}
		</div>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 marginTopMinus10">
		{if $FIELDS_HEADER}
			{foreach from=$FIELDS_HEADER key=LABEL item=VALUE}
				<div class="col-md-12 marginTB3 paddingLRZero">
					<div class="row col-lg-6 col-md-6 pull-right paddingLRZero">
						<button class="btn  {$VALUE['class']} btn-xs col-md-12">
							<div class="col-md-6 text-left">
								{vtranslate($LABEL,$MODULE_NAME)} 
							</div>
							<div class="col-md-6 paddingLRZero">
								<span class="badge marginTB3 paddingLR10">
									{$VALUE['value']}
								</span>
							</div>
						</button>
					</div>
				</div>
			{/foreach}
		{/if}
	</div>
{/strip}
