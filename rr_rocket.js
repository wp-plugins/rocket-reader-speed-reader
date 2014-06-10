/********************************************************************************************

	ROCKET READER JQUERY ENGINE

*********************************************************************************************/

// WPM FROM OPTIONS (WPM)
var wpm      = initWPM;
var delay_ms = parseInt(60000 / wpm);	// DELAY BETWEEN WORDS (MILLISECONDS)

jQuery( document ).ready(function() {
	var words;
	var word_ptr      = -1;
	var playing       = false;
	var currentPostID = 0;
	var letter_width  = 20;

	// VALUE FROM THE COOKIE OVERRIDES THE DEFAULT VALUE
	var tmp = getCookie("rr_wpm");
	if(typeof tmp != "undefined" && tmp != '' && tmp != 'NaN') wpm = parseInt(tmp);
	delay_ms = 60000 / wpm;

	// DISPLAY PLAY BUTTONS WHEN PAGE IS FULLY LOADED
	jQuery(".rr_btn_play").show();


	/****************************************************************************************
	 *
	 *	PLAY BUTTON PRESSED: START THE READER
	 *
	 ****************************************************************************************/
	rr_play = function(postID) {
		currentPostID = postID;
		jQuery("#rr_btn_play"+currentPostID).hide();
		jQuery("#rr_credits"+currentPostID).show();
		jQuery("#rr_reading_pane"+currentPostID).show();
		jQuery("#rr_btn_resume"+currentPostID).hide();
		jQuery("#rr_playing_controls"+currentPostID).show();			
		jQuery("#rr_delay"+currentPostID).show();
		rr_show_speed();
		// words = jQuery('#rr_content'+currentPostID).text().split(/\W+/);
		words = eval("words"+currentPostID);
		word_ptr = -1;
		playing  = true;
		rr_display_word();	
	} // rr_play()


	/****************************************************************************************
	 *
	 *	STOP BUTTON PRESSED: STOP THE READER
	 *
	 ****************************************************************************************/	
	rr_close = function() {
		playing = false;
		jQuery("#rr_btn_play"+currentPostID).show();
		jQuery("#rr_credits"+currentPostID).hide();
		jQuery("#rr_reading_pane"+currentPostID).hide();
		jQuery("#rr_playing_controls"+currentPostID).hide();	
		jQuery("#rr_delay"+currentPostID).hide();
	} // rr_close()


	/****************************************************************************************
	 *
	 *	PAUSE BUTTON PRESSED: PAUSE THE READER
	 *
	 ****************************************************************************************/	
	rr_pause = function() {
		playing = false;
		jQuery("#rr_btn_resume"+currentPostID).show();
		jQuery("#rr_btn_pause"+currentPostID).hide();
	} // rr_pause()


	/****************************************************************************************
	 *
	 *	RESUME BUTTON PRESSED: RESUME THE READER
	 *
	 ****************************************************************************************/	
	rr_resume = function() {
		jQuery("#rr_btn_resume"+currentPostID).hide();
		jQuery("#rr_btn_pause"+currentPostID).show();
		playing = true;
		rr_display_word();
	} // rr_resume()


	/****************************************************************************************
	 *
	 *	MINUS BUTTON PRESSED: REDUCE THE SPEED
	 *
	 ****************************************************************************************/	
	rr_minus = function() {
		if(wpm>20)
		{	wpm     -= 10;
			document.cookie="rr_wpm="+wpm+"; expires=Thu, 5 Dec 2999 12:00:00 GMT; path=/";
			delay_ms = parseInt(60000 / wpm);
			rr_show_speed();
		}		
	} // rr_minus()
	

	/****************************************************************************************
	 *
	 *	PLUS BUTTON PRESSED: INCREASE THE SPEED
	 *
	 ****************************************************************************************/	
	rr_plus = function() {
		wpm += 10;
		document.cookie="rr_wpm="+wpm+"; expires=Thu, 5 Dec 2999 12:00:00 GMT; path=/";
		delay_ms = parseInt(60000 / wpm);
		rr_show_speed();		
	} // rr_plus()
	

	/****************************************************************************************
	 *
	 *	DISPLAY THE CURRENT SPEED
	 *
	 ****************************************************************************************/		
	rr_show_speed = function()
	{
		jQuery("#rr_delay"+currentPostID).html("speed: <strong>"+wpm+"</strong> words / minute");
	} // rr_show_speed


	/****************************************************************************************
	 *
	 *	DISPLAY THE NEXT WORD
	 *
	 ****************************************************************************************/		
	rr_display_word = function() {
		
		if(!playing) return;
		
		if(word_ptr++>=words.length-1)
		{	// LAST WORD DISPLAYED
			playing = false;
			rr_close();
			return;
		}
		
		// NEXT WORD TO DISPLAY
		var wrd = words[word_ptr];
		
		if(wrd)
		{	// WORD IS NOT EMPTY
			var orp = parseInt(wrd.length / 2);
			
			// PREVIOUS CHAR FOR EVEN LENGTH WORDS
			if(wrd.length % 2 == 0) orp--;
			
			// FIND CLOSEST VOWEL TO THE LEFT
			orp = rr_find_vowel(wrd, orp);
			
			// COLOR THE ORP LETTER RED
			wrd = rr_mark_orp(wrd, orp);

			// SET BACKGROUND COLOR, BASED ON THE OPTION IN THE DATABASE
			jQuery("#rr_reading_pane"+currentPostID).css("background-color", initBgcolor);
			jQuery("#rr_reading_pane"+currentPostID).css("border-color", initBordercolor);			
			
			// CALCULATE THE LEFT POS OF THE WORD BASED ON THE ORP
			var widthRP  = jQuery("#rr_reading_pane"+currentPostID).width()/2;
			var leftpos  = widthRP - orp * letter_width;
			
			// ADJUST THE LEFT POS OF THE DIV
			jQuery("#rr_word"+currentPostID).css("left", leftpos+"px");
			
			var heightRP = (jQuery("#rr_reading_pane"+currentPostID).height()/2) - 32; // 12;
			jQuery("#rr_word"+currentPostID).css("top", heightRP+"px");
			
			// SET THE TEXT COLOR, BASED ON THE OPTION IN THE DATABASE
			jQuery("#rr_word"+currentPostID).css("color", initTextcolor);
			
			// STUFF THE WORD IN
			jQuery("#rr_word"+currentPostID).html(wrd);
		}
		
		// SET THE TIMER FOR THE NEXT WORD
		if(word_ptr>=words.length-1)
		    // LAST WORD: INCREASE THE DELAY
			setTimeout(rr_display_word, 3000);
		else
			setTimeout(rr_display_word, delay_ms);
			
	} // rr_display_word()


	/****************************************************************************************
	 *
	 *	MARK THE ORP (OPTICAL READING POINT / FOCUS POINT) IN A WORD
	 *
	 ****************************************************************************************/
	rr_mark_orp = function (wrd, orp)
	{
		// COLOR THE ORP LETTER RED
		return wrd.substr(0, orp)+'<span style="color:'+initFPcolor+'">'+wrd.substr(orp,1)+"</span>"+wrd.substr(orp+1); 
	} // rr_mark_orp()


	/****************************************************************************************
	 *
	 *	FIND THE FIRST VOWEL TO THE LEFT
	 *
	 ****************************************************************************************/	
	rr_find_vowel = function (wrd, orp)
	{
		// FIND THE FIRST VOWEL TO THE LEFT
		for(i=orp; i>=0; i--) if (/[aeiou]/i.test(wrd[i])) return i;
		// NO VOWELS FOUND TO THE LEFT OF THE CENTER; USE ORIGINAL ORP (= CENTER POINT)
		return orp;
	} // rr_find_vowel()
});


/********************************************************************************************

	GET A COOKIE

*********************************************************************************************/
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
} // getCookie()