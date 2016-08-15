<?php

	Security::init();

	echo UILayout::factory()
		->addObject(
			FFTextArea::factory('Old Text')
				->id('text1')
				->htmlWrap(FormFieldWrappers::CAPTION_TOP)
				->width('100%')
				->css('height', '560px')
			, '50% [padding: 10px 5px 10px 10px;]'
		)
		->addObject(
			FFTextArea::factory('New Text')
				->id('text2')
				->htmlWrap(FormFieldWrappers::CAPTION_TOP)
				->width('100%')
				->css('height', '560px')
			, '50% [padding: 10px 10px 10px 5px]'
		)
		->newLine()
		->addObject(
			FFButton::factory('Compare')
				->onClick('compare();')
				->width('100px')
			, '[float:right; padding: 0px 10px 0px 5px]'
		)
		->newLine()
		->addObject(
			UICustomHTML::factory(
				UIMessage::factory(
					UICustomHTML::factory()
						->id('restext')
						->css('text-align', 'left')
						->css('font-size', '16pt')
						->toHTML()
					, UIMessage::NOTE)
			)
			->id('msgvis')
			->css('display', 'none')
		)
		->toHTML();
?>

<script>
	function compare() {
		var text1 = $("#text1").val();
		var text2 = $("#text2").val();
		api.ajax.post(
			'texts_diff.ajax.php',
			{
				'text1': text1,
				'text2': text2
			},
			function(answer) {
				$('#msgvis').show();
				$('#restext').html(answer.res);
			}
		)
	}
</script>
