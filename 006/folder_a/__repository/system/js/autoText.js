function checkRequired() {
	ta = document.getElementsByTagName('TEXTAREA');
	for (i = 0; i < ta.length; i++) {
		if (ta[i].required) {
			if (ta[i].value == '') {
				alert('You did not fill out all required fields!');
				ta[i].style.border = '1px solid red';
				ta[i].focus();
				return false;
			}
		}
	}
}

function reduceAbit(obj) {
	oldScroll = obj.scrollHeight;
	oldHeight = obj.style.height;
	obj.style.height = Math.max(1 * obj.style.height.replace('px', '') - 10, obj.getAttribute('oldHeight')) + 'px';
	if (oldScroll == obj.scrollHeight) {
		obj.style.height = oldHeight;
		return false;
	} else {
		return true;
	}
}

function autoExpand(obj) {
	if (obj.value.length < obj.getAttribute('oldLength')) {
		while (reduceAbit(obj)) {
		}
		;
	}
	if (obj.scrollHeight > 1 * obj.style.height.replace('px', ''))     obj.style.height = obj.scrollHeight + 8 + 'px';
	obj.setAttribute('oldLength', obj.value.length);
}

function ieExpand(obj) {
	obj.style.height = Math.max(obj.scrollHeight + 8, obj.getAttribute('oldHeight'));
}

ta = document.getElementsByTagName('TEXTAREA');
for (i = 0; i < ta.length; i++) {
	if (ta[i].id) {
		ta[i].setAttribute('oldHeight', 1 * ta[i].style.height.replace('px', ''));
		if (document.all) {
			ta[i].onkeyup = new Function('ieExpand(this)');
		} else {
			ta[i].onkeyup = new Function('autoExpand(this)');
		}
		ta[i].onkeyup();
	}
}