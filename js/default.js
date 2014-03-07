function youWin() {
	document.getElementById('tah-dah').setAttribute('class', 'visible');
	document.getElementById('chalenge').setAttribute('class', 'hidden');
}

function resetChalenge() {
	document.getElementById('tah-dah').setAttribute('class', 'hidden');
	document.getElementById('chalenge').setAttribute('class', 'visible');
}

function toggleVisible(targetId) {
	var target = document.getElementById(targetId);
	var targetClass = target.getAttribute('class', 2);
	if (/hidden/.test(targetClass)) {
		targetClass = targetClass.replace(/hidden/, 'visible');
	} else {
		targetClass = targetClass.replace(/visible/, 'hidden');
	}
	target.setAttribute('class', targetClass);
}

