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
{if $AUTHENTICATED}
{* welcome only
<div id="welcome">
    <!--{$APP.NTC_WELCOME}, --><strong><a id="welcome_link" href='index.php?module=Users&action=EditView&record={$CURRENT_USER_ID}'>{$CURRENT_USER}</a></strong>{if !empty($LOGOUT_LINK) && !empty($LOGOUT_LABEL)} [ <a id="logout_link" href='{$LOGOUT_LINK}' class='utilsLink'>{$LOGOUT_LABEL}</a> ] &nbsp;&nbsp;{/if}

    <a id="welcome_link" href='index.php?module=Users&action=EditView&record={$CURRENT_USER_ID}'>{$CURRENT_USER}</a>
</div>
*}

{* welcome + global links *}
<div id="moduleList" class="welcome">
<ul>
    <li class="noBorder">&nbsp;</li>
    <li class="cssmenu settings">
        <span class="notCurrentTabLeft"></span><span class="notCurrentTab"><a id="welcome_link" href='index.php?module=Users&action=EditView&record={$CURRENT_USER_ID}'>{$CURRENT_USER}</a>
        <ul class="cssmenu settings">
        <a id="welcome_link" href='index.php?module=Users&action=EditView&record={$CURRENT_USER_ID}'{* title='{$CURRENT_USER}'*}><li>My Profile</li></a>
        {foreach from=$GCLS item=GCL name=gcl}
        <a href="{$GCL.URL}"{if !empty($GCL.ONCLICK)} onclick="{$GCL.ONCLICK}"{/if}><li>
        {$GCL.LABEL}
        {foreach from=$GCL.SUBMENU item=GCL_SUBMENU name=gcl_submenu}
        {if $smarty.foreach.gcl_submenu.first}
        <img src='{sugar_getimagepath file="menuarrow.gif"}' alt='' /><br />
        <ul class="cssmenu settings">
        {/if}
        <a href="{$GCL_SUBMENU.URL}"{if !empty($GCL_SUBMENU.ONCLICK)} onclick="{$GCL_SUBMENU.ONCLICK}"{/if}><li>{$GCL_SUBMENU.LABEL}</li></a>        
        {if $smarty.foreach.gcl_submenu.last}
        </ul>
        {/if}
        {/foreach}
        </li></a>
        {/foreach}

</div>
{/if}
{*
<div id="welcome">
   <a href="http://sugarcrm.specinfo.pl" border="0">hey just testing
    <img src="themes/SpecINFO_Blue/images/designedBy.png" width="152" height="21" 
        alt="SpecINFO" border="0"/>
    </a>
</div>
*}

