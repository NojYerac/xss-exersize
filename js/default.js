function youWin() {
	document.getElementById('tah-dah').setAttribute('class', 'visible');
	document.getElementById('chalenge').setAttribute('class', 'hidden');
}

function resetChalenge() {
	document.getElementById('tah-dah').setAttribute('class', 'hidden');
	document.getElementById('chalenge').setAttribute('class', 'visible');
}
