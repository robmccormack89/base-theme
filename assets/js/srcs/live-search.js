// live search
jQuery(function($) {

	// helper function to highlight search results text
	$.fn.wrapInTag = function(opts) {
		function getText(obj) {
			return obj.textContent ? obj.textContent : obj.innerText;
		}
		var tag = opts.tag || 'strong',
			words = opts.words || [],
			regex = RegExp(words.join('|'), 'gi'),
			replacement = '<' + tag + '>$&</' + tag + '>';
		$(this).contents().each(function() {
			if (this.nodeType === 3) {
				$(this).replaceWith(getText(this).replace(regex, replacement));
			} else if (!opts.ignoreChildNodes) {
				$(this).wrapInTag(opts);
			}
		});
	};

  // set some element vars
  var theForm = document.getElementById('form_validate_search');
	var theFormNavItemCloseButton = document.getElementById('CloseSearchNav');
  var results = $('#response_search_results');
  var spinner = $('#loaderSearchToggle');
  var reseter = $('#LiveSearchReset');

	// hide & show the reset icon in the live search form - done separate so not have debounce
	$(document).on("input", "#input_search", function() {
    var query = $(this).val();
    if (query.length > 0) {
			reseter.show();
		} else {
      reseter.hide();
    }
	});

	// reset the form when clicking the nav item close button for the search
	theFormNavItemCloseButton.addEventListener("click", function() {
		theForm.reset();
  });

	// hide the reseter on actual reset events to the form
	theForm.addEventListener("reset", function() {
    reseter.hide();
		results.hide();
  });

	// live search js (jquery, should do this in plain js at some point)
	$(document).on("input", "#input_search", debounce(function() {

    // set some vars
		var query = $(this).val();
		var req;

		if (query.length < 2) {
			results.hide();
      // alert('query length wrong');
			return false;
		}

		results.hide();
		spinner.show(); // works

		if (req != null) req.abort();

		req = $.ajax({
			type: 'post', 
			url: myAjax.ajaxurl,
			data: {
				action: 'ajax_live_search',
				query: query
			},
			success: function(response) {
				if (!response) {
					// alert('empty response');
					results.hide();
					spinner.show();
					return;
				}
				var obj = JSON.parse(response);
				if (obj.result == 1) {
          // alert('response');
					spinner.hide();
          document.getElementById("response_search_results").innerHTML = obj.response; // using .innerHTML requires using getElementById() rather than $jQuery
					results.show();
				}
				$('#response_search_results ul li a').wrapInTag({
					words: [query]
				});
			},
			error: function(request, status, error) {
        // alert(error);
        // console.log(request);
				spinner.show();
				results.hide();
			}
		});

	}, 500));

	// search results hide on additional click away
	$(document).on('click', function(e) {
		if ($(e.target).closest(".site-header").length === 0) {
			results.hide();
			// theForm.reset();
		}
	});

});