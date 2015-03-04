var ls = ls ||
{};

ls.stickytopics = ls.stickytopics ||
{};

ls.stickytopics = (function($)
{
	this.targetType='';
	this.targetId=0;
	this.wasSearch=false;
	
	this.options =
	{}

	this.reloadStickyTopics = function()
	{
		thisObj=this;
		$('#stickytopics_list').addClass('loader');
		ls.ajax(aRouter.ajax + 'stickytopics/reload',
		{
			'targetType': this.targetType,
			'targetId': this.targetId
		}, function(response)
		{
			$('#stickytopics_list').removeClass('loader');
			if (!response.bStateError)
			{
				$('#stickytopics_list').html(response.topicData);
				if ($("#search_topic").length)
					thisObj.findTopics($("#search_topic").val());
			}
			else
			{
				ls.msg.error(response.sMsgTitle, response.sMsg);
			}
		});
	}

	this.getStickyTopicsList = function(titlePart)
	{
		aRes=new Array();
		$('.st_stickytopic').each(
				function (index,el)
				{
					aRes.push(el.id.substr(12));
				}
				);
		return aRes;
	}
	
	this.findTopics = function(titlePart,force)
	{
		if (!force && !this.wasSearch)
			return;
		
		aList=this.getStickyTopicsList();
		ls.ajax(aRouter.ajax + 'stickytopics/find',
		{
			'targetType': this.targetType,
			'targetId': this.targetId,
			'excludeTopics': aList,
			'titlePart' : titlePart
		}, function(response)
		{
			if (!response.bStateError)
			{
				$('#stickytopics_find_list').html(response.topicData);
			}
			else
			{
				ls.msg.error(response.sMsgTitle, response.sMsg);
			}
		});
		
		this.wasSearch=true;
	}

	this.addTopic = function(topicId)
	{
		var thisObj=this;
		ls.ajax(aRouter.ajax + 'stickytopics/add',
		{
			'topicId': topicId,
			'targetType': this.targetType,
			'targetId': this.targetId
		}, function(response)
		{
			thisObj.reloadStickyTopics();
			
			if (!response.bStateError)
			{
			}
			else
			{
				ls.msg.error(response.sMsgTitle, response.sMsg);
			}
		});
	}

	this.moveTopic = function(topicId,dir)
	{
		var thisObj=this;
		ls.ajax(aRouter.ajax + 'stickytopics/move',
		{
			'topicId': topicId,
			'direction': dir,
			'targetType': this.targetType,
			'targetId': this.targetId
		}, function(response)
		{
			thisObj.reloadStickyTopics();
			
			if (!response.bStateError)
			{
			}
			else
			{
				ls.msg.error(response.sMsgTitle, response.sMsg);
			}
		});
	}

	this.deleteTopic = function(topicId)
	{
		var thisObj=this;
		ls.ajax(aRouter.ajax + 'stickytopics/delete',
		{
			'topicId': topicId,
			'targetType': this.targetType,
			'targetId': this.targetId
		}, function(response)
		{
			thisObj.reloadStickyTopics();
			
			if (!response.bStateError)
			{
			}
			else
			{
				ls.msg.error(response.sMsgTitle, response.sMsg);
			}
		});
	}

	this.init = function()
	{
	}

	return this;
}).call(ls.stickytopics ||
{}, jQuery);

jQuery(document).ready(function()
{
	ls.stickytopics.init();
});