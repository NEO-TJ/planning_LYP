//************************************************ Helper **********************************************
//---------------------------------------------- Date Time ---------------------------------------------
Date.prototype.increaseOneDay = function(excludeSunday) {
	if(isEmpty(excludeSunday)) { excludeSunday = false; }
	
	this.setDate(this.getDate() + 1);
	if((excludeSunday) && (this.getDay() == 0)) {
		this.setDate(this.getDate() + 1);
	}
	
	return this;
};
Date.prototype.decreaseOneDay = function(excludeSunday) {
	if(isEmpty(excludeSunday)) { excludeSunday = false; }
	
	this.setDate(this.getDate() - 1);
	if((excludeSunday) && (this.getDay() == 0)) {
		this.setDate(this.getDate() - 1);
	}
	
	return this;
};
Date.prototype.addDays = function(days) {
	this.setDate(this.getDate() + parseInt(days,10));
	return this;
};
