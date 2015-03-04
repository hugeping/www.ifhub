/**
 * Spoiler :: jQuery
 * Modified by fedorov mich © 2014
 * [ LS :: 1.0.3 | Habra Style ]
 */
$(document).ready(function(){
 $('.spoiler-title').click(function(){
  $(this).toggleClass('open');
  $(this).parent().children('div.spoiler-body').toggle('normal');
  return false;
 });
});