<!-- BEGIN: MAIN -->

<h2>{PHP.L.FAQ}</h2>
{FILE "{PHP.cfg.themes_dir}/{PHP.theme}/warnings.tpl"}
<div class=" button-toolbar block">
		<a title="{PHP.L.Configuration}" href="{ADMIN_FAQ_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
		<a href="{ADMIN_FAQ_CATEGORIES_URL}" class="button">{PHP.L.Categories}</a></li>
		<a href="{ADMIN_FAQ_QUESTION_ADD_URL}" class="button special">{PHP.L.faq_add_question}</a>
</div>

<div class="block">
	<h3>{PHP.L.faq_questions}:</h3>
	<table class="cells">
		<tr>
			<td class="right" colspan="4">
				<form id="queue_filter" name="form_questions_queue" method="get" action="{ADMIN_FAQ_FORM_FILTER_URL}">
				<input name="m" type="hidden" value="faq"/>
				<span style="text-align: middle;">{PHP.L.faq_sort}</span> &nbsp; {ADMIN_FAQ_ORDER} &nbsp;  {ADMIN_FAQ_WAY} &nbsp; &nbsp;
				<span style="text-align: middle;">{PHP.L.Show}</span> {ADMIN_FAQ_FILTER} &nbsp; 
				{ADMIN_FAQ_ONE_PAGE} &nbsp; &nbsp;
				<button type="submit">{PHP.L.Filter}</button>
				</form>
			</td>
		</tr>
		<tr>
			<!-- IF {PHP.faq_uses_categories} -->
				<td class="coltop width5">{PHP.L.faq_position}</td>
				<td class="coltop width15">{PHP.L.Category}</td>
				<td class="coltop width55">{PHP.L.faq_question}</td>
				<td class="coltop width20">{PHP.L.Action}</td>
			<!-- ELSE -->
				<td class="coltop width5">{PHP.L.faq_position}</td>
				<td class="coltop width70">{PHP.L.faq_question}</td>
				<td class="coltop width20">{PHP.L.Action}</td>
			<!-- ENDIF -->
		</tr>
		<form id="form_questions_queue" name="queue_update" method="post" action="{ADMIN_FAQ_FORM_UPDATE_URL}">
<!-- BEGIN: ADMIN_FAQ_QUESTIONS -->
		<tr>
			<td class="centerall">
				{ADMIN_FAQ_QUESTION_POSITION}
			</td>
			<!-- IF {PHP.faq_uses_categories} -->
			<td>
				{ADMIN_FAQ_QUESTION_CAT}
			</td>
			<!-- ENDIF -->
			<td>
				<div>
					{ADMIN_FAQ_QUESTION_TEXT}
				</div>
			</td>
			<td class="action" style="text-align: center;">
				<a title="{PHP.L.faq_answer_and_edit}" href="{ADMIN_FAQ_QUESTION_MANAGE_URL}" class="button">{PHP.L.faq_answer} / {PHP.L.Edit}</a>
				<a title="{PHP.L.Delete}" href="{ADMIN_FAQ_QUESTION_DELETE_URL}" class="confirmLink button">{PHP.L.short_delete}</a>
			</td>
		</tr>
<!-- END: ADMIN_FAQ_QUESTIONS -->
<!-- BEGIN: ADMIN_FAQ_NO_QUESTIONS -->
		<tr>
			<td class="centerall" colspan="4">{PHP.L.None}</td>
		</tr>
<!-- END: ADMIN_FAQ_NO_QUESTIONS -->
	</table>
	<div class="block" style="margin-top: 15px; text-align: center;">
		<button type="submit">{PHP.L.Update}</button>
	</div>
	</form>
	<!-- IF !{PHP.op} -->
		<p class="paging">
			{ADMIN_FAQ_PAGENAV_PREV}{ADMIN_FAQ_PAGENAV_MAIN}{ADMIN_FAQ_PAGENAV_NEXT}<span>{PHP.L.Total}: {ADMIN_FAQ_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_FAQ_ON_PAGE}</span>
		</p>
	<!-- ENDIF -->
	</form>
</div>

<!-- END: MAIN -->