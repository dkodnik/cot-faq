<!-- BEGIN: MAIN -->

<!-- IF {PHP.faq_uses_categories} && {PHP.faq_has_subcategories} -->
<div class="col3-2 first">
<!-- ELSE -->
<div>
<!-- ENDIF -->
	<div class="block">
		<h2>{FAQ_PATH}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

		<!-- IF {PHP.faq_has_questions} -->

			<div class="odd width70" style="border: 1px solid #eee;">
				<ul style="padding: 7px; padding-top: 10px; line-height: 200%;">
				<!-- BEGIN: FAQ_LIST_QUESTIONS -->
					<li style="padding-bottom: 3px;">{FAQ_LIST_QUESTION_LINK}</li>
				<!-- END: FAQ_LIST_QUESTIONS -->
				</ul>
			</div>

			<!-- BEGIN: FAQ_QUESTIONS_AND_ANSWERS -->
			<div style="margin-top: 15px;">
				<h5 style="font-size: 14px;">{FAQ_QAA_QUESTION_TEXT}</h5>
				<!-- BEGIN: IS_ADMIN -->
				<div style="margin-top: 10px; font-size: 11px;">
					{PHP.L.Options}: 
					&nbsp;
					{FAQ_QAA_QUESTION_DELETE_LINK}
					&nbsp;
					{FAQ_QAA_QUESTION_EDIT_LINK}
				</div>
				<!-- END: IS_ADMIN -->
				<div style="margin-top: 10px;">
					{FAQ_QAA_QUESTION_ANSWER_TEXT}
				</div>
			</div>
			<!-- END: FAQ_QUESTIONS_AND_ANSWERS -->

		<!-- ENDIF -->
	</div>

	<!-- IF !{PHP.faq_has_questions} -->
		<div class="block">
			{PHP.L.faq_no_questions}
		</div>
	<!-- ENDIF -->

	<div class="block">
		<!-- BEGIN: FAQ_QUESTION_ADD -->
		<h2>{PHP.L.faq_add_question}</h2>

		<form action="{FAQ_QUESTION_ADD_FORM_SEND}" method="post" name="question_add">
			<table class="flat">
				<!-- BEGIN: GUEST -->
				<tr>
					<td class="width30">{PHP.L.Username}</td>
					<td class="width70">{FAQ_QUESTION_ADD_GUEST_USERNAME}</td>
				</tr>
				<tr>
					<td>{PHP.L.Email}</td>
					<td> {FAQ_QUESTION_ADD_GUEST_EMAIL}</td>
				</tr>
				<tr>
					<td>{FAQ_QUESTION_ADD_VERIFYIMG}</td>
					<td>{FAQ_QUESTION_ADD_VERIFY}</td>
				</tr>
				<!-- END: GUEST -->
				<tr>
					<td colspan="2">{FAQ_QUESTION_ADD_TEXT}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid"><button type="submit">{PHP.L.Submit}</button></td>
				</tr>
			</table>
		</form>

		<!-- END: FAQ_QUESTION_ADD -->

	</div>
</div>

<!-- IF {PHP.faq_uses_categories} && {PHP.faq_has_subcategories} -->
<div class="col3-1">

	<div class="block">
		<h2>{PHP.L.Categories}</h2>
		<ul style="margin: 10px;">
			<!-- BEGIN: FAQ_CATEGORIES -->
				<li style="padding-top: 5px;">
					<strong><a href="{FAQ_CATEGORY_URL}">{FAQ_CATEGORY_TITLE} ({FAQ_CATEGORY_QUESTION_COUNT_TOTAL})</a></strong>
				</li>
				<li>{FAQ_CATEGORY_DESC}</li>
			<!-- END: FAQ_CATEGORIES -->
		</ul>
	</div>
</div>
<!-- ENDIF -->

<!-- END: MAIN -->