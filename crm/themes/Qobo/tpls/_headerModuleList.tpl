{*
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

*}

<div id="moduleList">
 {if $AUTHENTICATED}
<ul>
    <li class="noBorder">&nbsp;</li>
    {foreach from=$moduleTopMenu item=module key=name name=moduleList}
    <!--if $name == $MODULE_TAB-->
{if $name=='foo'}
      {if $name=='Home'}
	 <li class="noBorder">
	 <!--<div id="noBorderclass">-->
        <span class="currentTabLeft"></span><span class="currentTab">{sugar_link id="moduleTab_$name" module=$name}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a>-->
	<ul class="cssmenu"></ul>
	</span><span class="currentTabRight"></span>
	<!--</div>-->
      {else}
    <li class="noBorder">
    <!--<div id="noBorderclass">-->
        <span class="currentTabLeft"></span><span class="currentTab">{sugar_link id="moduleTab_$name" module=$name}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a>-->
	<ul class="cssmenu">
	{foreach from=$shortcutTopMenu.$name item=shortcut_item}
		<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{sugar_ajax_url url=$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a><!--<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a>-->
	{/foreach}
	</ul>
	</span><span class="currentTabRight"></span>
{/if}
	<!--</div>-->
{else}
        {if $name=='Home'}
{*
	    <li>
        <span class="notCurrentTabLeft"></span><span class="notCurrentTab">{sugar_link id="moduleTab_$name" module=$name data=$module}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a>-->
	</span><span class="notCurrentTabRight"></span>
    
    before new recent
        <span class="notCurrentTabLeft"></span><span class="notCurrentTab">{include file="_companyLogo.tpl" theme_template=true}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a>-->
	</span><span class="notCurrentTabRight"></span>
*}
	    <li>
        <span class="notCurrentTabLeft"></span><span class="notCurrentTab">{include file="_companyLogo.tpl" theme_template=true}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a>-->
	<ul class="cssmenu homemenu">
	{foreach from=$shortcutTopMenu.$name item=shortcut_item}
		<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{sugar_ajax_url url=$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a><!--<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a>-->
	{/foreach}
    {assign var=index value=0}
    {foreach from=$recentRecords item=item name=lastViewed}
        {assign var=index value=$index+1}
{if $index==1}
        <li id="recentViewText">Recently Viewed</li>
{/if}
{* Change title of link from ZuckerReports to Reports *}
{assign var=title value=$item.cust_module_label}
{if $item.cust_module_label=="ZuckerReports"}
    {assign var=title value="Reports"}
{/if}
        <a title="{$title}" href="{sugar_link module=$item.module_name action='DetailView' record=$item.item_id link_only=1}">
            <li>{$item.image}&nbsp;{$item.item_summary_short}</li>
        </a>
    {/foreach}
	</ul>
	</span><span class="notCurrentTabRight"></span>
    
    
	{else}
    <li>
        <span class="notCurrentTabLeft"></span><span class="notCurrentTab">{sugar_link id="moduleTab_$name" module=$name data=$module}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a>-->
	<ul class="cssmenu">
    {assign var=index value=0}
	{foreach from=$shortcutTopMenu.$name item=shortcut_item}
        {assign var=index value=$index+1}
{if $index==1}
        <li id="recentViewText">Actions</li>
{/if}
{* Only userid=1 can view all options for ZuckerReports (excluding about) *}
{*
{if (($name=="zr2_Report") && 
    ( (($shortcut_item.MODULE_NAME=="zr2_About")) || 
    (($CURRENT_USER_ID!=1) && (($shortcut_item.MODULE_NAME!="zr2_ReportOnDemand")&&($shortcut_item.MODULE_NAME!="zr2_ReportContainer"))) ) ) }
    
{else}
*}
		<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{sugar_ajax_url url=$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a><!--<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a>-->
{*
{/if}
*}
	{/foreach}
    {assign var=index value=0}
    {foreach from=$cust_recentRecordsPerModule.$name item=item name=lastViewed}
        {assign var=index value=$index+1}
{if $index==1}
        <li id="recentViewText">Recently Viewed</li>
{/if}
        <a href="{sugar_link module=$item.module_name action='DetailView' record=$item.item_id link_only=1}">
            <li>{$item.image}&nbsp;{$item.item_summary_short}</li>
        </a>
{*
        <a href="{sugar_link module=$item.module_name action='DetailView' record=$item.item_id link_only=1}">
            <li{if $index==1} id="recentView"{/if}>{$item.image}&nbsp;{$item.item_summary_short}</li>
        </a>
*}
    {/foreach}
	</ul>
	</span><span class="notCurrentTabRight"></span>
	{/if}
    {/if}
    </li>
    {/foreach}
    {if count($moduleExtraMenu) > 0}
    <li id="moduleTabExtraMenu">
        <a href="#">&gt;&gt;</a><br />
        <ul class="cssmenu">
        {foreach from=$moduleExtraMenu item=module key=name name=moduleList}
            <li>{sugar_link id="moduleTab_$name" module=$name data=$module}<!--<a id="moduleTab_{$name}" module="{$name}" href="?module={$name}&action=index">{$module}</a><font color="black"></font>-->
            {if $shortcutExtraMenu.$name}
                <ul class="cssmenu1">
                {assign var=index value=0}
                {foreach from=$shortcutExtraMenu.$name item=shortcut_item}
                    {assign var=index value=$index+1}
{if $index==1}
        <li id="recentViewText">Actions</li>
{/if}
                    <a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{sugar_ajax_url url=$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a><!--<a id="{$shortcut_item.LABEL|replace:' ':''}{$tabGroupName}" href="{$shortcut_item.URL}"><li>{$shortcut_item.IMAGE}&nbsp;{$shortcut_item.LABEL}</li></a>-->
                {/foreach}
                {assign var=index value=0}
                {foreach from=$cust_recentRecordsPerModule.$name item=item name=lastViewed}
                    {assign var=index value=$index+1}
{if $index==1}
        <li id="recentViewText">Recently Viewed</li>
{/if}
                    <a href="{sugar_link module=$item.module_name action='DetailView' record=$item.item_id link_only=1}">
                        <li>{$item.image}&nbsp;{$item.item_summary_short}</li>
                    </a>
{*
                    <a href="{sugar_link module=$item.module_name action='DetailView' record=$item.item_id link_only=1}">
                        <li{if $index==1} id="recentView"{/if}>{$item.image}&nbsp;{$item.item_summary_short}</li>
                    </a>
*}
                {/foreach}
            </ul>
            {/if}	    
	    </li>
        {/foreach}
        </ul>        
    </li>
    {/if}
</ul>
{/if}
</div>
{*{if $AUTHENTICATED}{include file="_headerLastViewed.tpl" theme_template=true}{/if}*}