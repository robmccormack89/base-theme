// ajax search 
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

	// ajax search js
	$(document).on("input", "#input_search", debounce(function() {
		var input_value = $(this).val();
		var query = input_value;
		var req;

		if (query.length < 2) {
			$('#response_search_results').hide();
			return false;
		}

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
					alert('empty');
					$('#response_search_results').hide();
					return;
				}
				var obj = JSON.parse(response);
				if (obj.result == 1) {
					document.getElementById("response_search_results").innerHTML = obj.response;
					$('#response_search_results').show();
				}
				$('#response_search_results ul li a').wrapInTag({
					words: [input_value]
				});
			},
			error: function(request, status, error) {
				$('#response_search_results').hide();
			}
		});
	}, 500));

	// search results hide on additional click away
	$(document).on('click', function(e) {
		if ($(e.target).closest(".top-search-bar").length === 0) {
			$("#response_search_results").hide();
		}
	});

});

function start(){
  var hmm = document.getElementById("product_cat")
	if(hmm){
		hmm.addEventListener("change", getSubCatFromParent, false);
	}
	var moo = document.getElementById("product_series")
	if(moo){
		moo.addEventListener("change", getModelFromSeries, false);
	}
	var bar = document.getElementById("adv_reset")
	if(bar){
		bar.addEventListener("click", mehhhh, false);
	}
	
  // document.getElementById("product_series").addEventListener("change", getModelFromSeries, false);
  // document.getElementById("adv_reset").addEventListener("click", mehhhh, false);
}

function mehhhh(event){
  var uno = document.getElementById("product_cat_sub")
  var duex = document.getElementById("product_series_sub")
  uno.disabled = true;
  duex.disabled = true;
}

function getSubCatFromParent(event){
  var route = document.location.origin  + '/wp-json/get_subcats'
  getSubTerms(event, route);
}
function getModelFromSeries(event){
  var route =  document.location.origin  + '/wp-json/get_submodels'
  getSubTerms(event, route);
}
function getSubTerms(e, route){
  
  var sel = e.target; // target the parent select element, which is target of the change event
  var opt = sel.options[sel.selectedIndex]; // look at the options of the parent select element & get the selected one
  
  var sel2_id = e.target.id +  '_sub';
  var sel2 = document.getElementById(sel2_id); // target the sub select dynamically
  sel2.disabled = true;
  // sel2.parentElement.hidden = true; // sub select should be disabled by default
  
  // we only want to continue if the select has a value to start...
  if(opt.value) {
    
    const parent_data = {
      slug: opt.value,
      id: opt.id,
    };
    
    fetch(route, {
      method: "POST",
      credentials: 'same-origin',
      headers: {
        // 'Content-Type': 'text/html; charset=UTF-8',
        "Content-Type": "application/json"
      },
      body: JSON.stringify(parent_data),
    })

    .then((response) => response.json())

    .then((data) => {
      if (data) {
        // console.log(data);
        sel2.innerHTML = data;
        sel2.disabled = false;
        // sel2.parentElement.hidden = false; // we should reenable sel2 only when we have valid subs, on successful fetch()
      }
    })

    .catch((error) => {
      console.error(error);
    });
    
  }
  
}

window.addEventListener("load", start, false); // start the select element event listners