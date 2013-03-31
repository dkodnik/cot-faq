<!-- BEGIN: MAIN -->

<h2>{PHP.L.faq_question}</h2>
{FILE "{PHP.cfg.themes_dir}/{PHP.theme}/warnings.tpl"}
<form method="post" name="manage_question" action="{ADMIN_FAQ_MANAGE_FORM_URL}">
<div class="block">
	<table class="cells">

		<!-- IF {PHP.id} > 0 -->
		<tr>
			<td><strong>{PHP.L.Id}</strong></td>
			<td>{ADMIN_FAQ_MANAGE_QUESTION_ID}</td>
		</tr>
		<!-- ENDIF -->
		<tr>
			<td><strong>{PHP.L.faq_position}</strong></td>
			<td>{ADMIN_FAQ_MANAGE_QUESTION_POSITION}</td>
		</tr>
		<!-- IF {PHP.faq_uses_categories} -->
		<tr>
			<td><strong>{PHP.L.Category}</strong></td>
			<td>{ADMIN_FAQ_MANAGE_QUESTION_CAT}</td>
		</tr>
		<!-- ENDIF -->
		<tr>
			<td><strong>{PHP.L.Username}</strong></td>
			<td>{ADMIN_FAQ_MANAGE_QUESTION_USERNAME}</td>
		</tr>
		<tr>
			<td><strong>{PHP.L.Email}</strong></td>
			<td>{ADMIN_FAQ_MANAGE_QUESTION_USEREMAIL}</td>
		</tr>
		<tr>
			<td><strong>{PHP.L.faq_question}</strong></td>
			<td>{ADMIN_FAQ_MANAGE_QUESTION_TEXT}</td>
		</tr>
	</table>
</div>

<h2>{PHP.L.faq_answer}</h2>

<div class="block">
	<table>
		<tr>
			<td class="width20"><strong>Approved for display ?</strong></td>
			<td class="width80">{ADMIN_FAQ_MANAGE_QUESTION_APPROVED}</td>
		</tr>
		<tr>
			<td style="padding-top: 25px;" colspan="2">{ADMIN_FAQ_MANAGE_ANSWER_TEXT}</td>
		</tr>
	</table>
</div>

<div class="block" style="text-align: center;">
	<button type="submit">{PHP.L.Update}</button>
</div>
</form>
<!-- END: MAIN -->