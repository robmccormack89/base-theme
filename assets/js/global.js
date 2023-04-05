// Function called before the popup for media info is displayed. Content and title can be changed. used in nano-gallery: justified 
function my_popup_info(item, title, content){
	var my_title = title + ' <b>nanogallery2</b>';
	var my_content = content + '<br><br>[The content of this popup can be customized with some javascript.]';
	return {title: my_title, content: my_content};
}

// just the inf scroll pagination init
function archivePagination(items, item, next, loader, hider, button, disableHistory, disablescrollThreshold) {
	if(items){
		let pagi_elem = document.querySelector(items);
		if(pagi_elem){
			var argsObj = {};
			if(item)argsObj['append'] = item;
			if(next)argsObj['path'] = next;
			if(loader)argsObj['status'] = loader;
			if(hider)argsObj['hideNav'] = hider;
			if(button)argsObj['button'] = button; // load pages on button click
			if(disableHistory)argsObj['history'] = false;
			if(disablescrollThreshold)argsObj['scrollThreshold'] = false; // disable loading on scroll
			let infScroll = new InfiniteScroll(pagi_elem, argsObj);
		}
	}
}

//
// blog filters: main functions
//

// submit the filters form by interuppting normal submit & doing our own
function filtersFormSubmitCustom(event, form, demoPostsGrid, postsGrid, theLoopContainer, theOldLoopSel, subFields, formFields, hidden_cls){

	var demoPostsGrid = document.getElementById(demoPostsGrid);
	var postsGrid = document.getElementById(postsGrid);
	var theLoopContainer = document.getElementById(theLoopContainer);
	var theOldLoop = document.querySelector(theOldLoopSel);

	// event.preventDefault(); // prevent default submit event. this is done when calling this function instead

	// disable any checked parent checkboxes when subs are also selected.
	for (i in subFields) {
		if(subFields[i].parentGroupId){
			disableParentWhenParentAndChildSelected(subFields[i].parentGroupId, subFields[i].subGroupId);
		}
	}

	// process form data into URLSearchParams
	const formData = new FormData(form);
	const formParams = new URLSearchParams(formData);

	// instead, we use set on formsParams data for comma separated values. see URLSearchParams.set()
	// we check they exist first.. dont want to add a param to the url when it has no selections
	// do this for every defined field grouping
	for (i in formFields) {
		if(formParams.has(formFields[i])) formParams.set(formFields[i], formParams.getAll(formFields[i]));
	}

	// the encoded/decoded uri & query strings
	const encodedParams = formParams.toString();
	const decodedParams = decodeURIComponent(encodedParams);
	const queryString = form.action + '?' + decodedParams;

	// start the spinners. unhide the demo posts grid
	demoPostsGrid.classList.remove(hidden_cls);
	postsGrid.textContent = ''; // proper posts grid becomes empty, only for now

	// the fetch request.
	// we need to grab only a certain HTML element from the returned request...
	// and then replace the equivalent HTML element on the current page with that...
	// whilst adding in the loading animation effect in between the transitions...
	// we will also want to replace the url in the browser when we're done...

	// the fetching
	fetch(queryString)

	// fetching response
	.then(function(response) {
		return response.text();
	})

	// do stuff with returned html 
	.then(function(html) {

		// setup the data
		var parser = new DOMParser();
		var newDocObj = parser.parseFromString(html, 'text/html');

		// replace the old content with the new (whilst hiding the demo posts grid)
		var theNewLoop = newDocObj.querySelector(theOldLoopSel);
		demoPostsGrid.classList.add(hidden_cls);
		theLoopContainer.replaceChild(theNewLoop, theOldLoop);

		// dispatch a new event
		const filtersFormSubmissionSuccess = new Event('filtersFormSubmissionSuccess');
		form.dispatchEvent(filtersFormSubmissionSuccess);

		// console.log('the content has been replaced');

	})

	.catch(error => {

		// console.error('Somethings gone wrong...', error);

	});

}
// we need to fetch get the sub-terms when we select a parent term as a filter. 
function getSubDropsFromParentSelection(event, q_vars, tax_key, q_key, location = '/wp-json/get_subcats', group_id = 'post_cat_group', sub_group_id = 'post_subcat_group', sub_id = 'post_cat_sub', hidden_cls = 'uk-hidden', loader_string = 'loader_'){

	var route = document.location.origin + location

	// Create an Array.
	var selected = new Array();
	var id = new Array();

	// Reference the Table.
	var groupTable = document.getElementById(group_id);
	var spinner = loader_string + sub_id;

	// Reference all the CheckBoxes in Table.
	var chks = groupTable.getElementsByTagName('input');

	// Loop and push the checked CheckBox value in Array.
	for (var i = 0; i < chks.length; i++) {
		if (chks[i].checked) {
			selected.push(chks[i].value);
			id.push(chks[i].id);
		}
	}

	// when selecteds DO exist (at least 1)
	if (selected.length > 0 && Array.isArray(selected)) {

		// hide the sub if not already hidden & unhide the spinner
		document.getElementById(sub_id).classList.add(hidden_cls);
		document.getElementById(spinner).classList.remove(hidden_cls);

		const parent_data = {
			slug: selected,
			id: id,
			q_vars: q_vars,
			tax_key: tax_key,
			q_key: q_key
		};

		fetch(route, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(parent_data),
		})

    .then((response) => {
      return response.json();
    })

		.then((data) => {
			if (data) {

				// place the new data in html, hide & remove stuff
				document.getElementById(sub_group_id).innerHTML = data;
				document.getElementById(spinner).classList.add(hidden_cls);
				document.getElementById(sub_id).classList.remove(hidden_cls);

				// create a new event when all done
				const jingo = new Event('jingo');
				document.getElementById(sub_id).dispatchEvent(jingo);

			} else { 
				
				// we are here when there is no data coming back.... 
			
			}
		})

		.catch((error) => {

			// on error, we wanna re-hide stuff etc...
			document.getElementById(spinner).classList.add(hidden_cls);
			document.getElementById(sub_id).classList.add(hidden_cls);
			
		});

	}

}
// does the functionality to allow the toggling of filter button to filter area
function toggleDropsAndAreas(btn_sel = '.filter-btn', drop_target = 'drop_target', show_cls = 'theme-show', area_sel = '.filter-area', active_cls = 'theme-active'){

	// all of the filter buttons (grouped)
	const filterButtons = document.querySelectorAll(btn_sel);

	// loop thru filter buttons to add custom js UIkit.toggle's to them
	for (var i = 0; i < filterButtons.length; i++) {
		(function () {

			// set vars
			var id = filterButtons[i].id;
			var target = filterButtons[i].getAttribute(drop_target);
			var targetsButton = document.querySelector('a[' + drop_target + '="' + target + '"]');

			// add the UIkit.toggle to the filterButton item
			UIkit.toggle('#' + id, {
				target: target,
				cls: show_cls
			});

			// show & hide stuff on particular events firing on the target element
			// remember: the target element is defined FROM filterButtons.item we are in (in the loop)
			// so we are adding event listeners to the target element FOREACH filter button with a target

			document.querySelector(target).addEventListener('beforeshow', function(event) {
				removeClassFromElements(area_sel, show_cls) // hide filter area/s
			})
			document.querySelector(target).addEventListener('shown', function(event) {
				removeClassFromElements(btn_sel, active_cls) // remove active class from filter buttons
				targetsButton.classList.add(active_cls) // add active class to target buttons
			})
			document.querySelector(target).addEventListener('hidden', function(event) {
				targetsButton.classList.remove(active_cls) // remove active class from target buttons
			});

		}()); // immediate invocation
	}

}

//
// blog filters: helpers
//

// used in filtersFormSubmitCustom()
function disableParentWhenParentAndChildSelected(parent_group, child_group, parent_attr = 'data-parent'){

	var _parent_group = document.getElementById(parent_group);
	var chks_parent_group = _parent_group.getElementsByTagName('INPUT');

	var _child_group = document.getElementById(child_group);
	var chks_child_group = _child_group.getElementsByTagName('INPUT');

	var arr = [];
	for (var i = 0; i < chks_child_group.length; i++) {
		if(chks_child_group[i].checked){
			arr.push(chks_child_group[i].getAttribute(parent_attr));
		}
	}

	// need to now get second array of checked boxes's values in parent group
	// and look thru that, for any matches to the first array...
	// if any items in second arr match any of the items in first arr, those items in second arr should be disabled
	// loop thru for the second array (the parent group)

	var arrTwo = [];
	for (var i = 0; i < chks_parent_group.length; i++) {
		if(arr.includes(chks_parent_group[i].value) &&  chks_parent_group[i].checked){
			chks_parent_group[i].disabled = true;
		}
	}

}
// used in archive.twig & addQuickLoadToDataLinkAttrs()
function reEnableDisabledCheckboxes(id){
	var group = document.getElementById(id);
	var chks = group.getElementsByTagName('INPUT');
	for (var i = 0; i < chks.length; i++) {
		if (chks[i].disabled) {
			chks[i].disabled = false;
		}
	}
}
// used in archive.twig
function addClassToElements(ele, cls){
	var elementsToTarget = document.querySelectorAll(ele);
	for (var i = 0; i < elementsToTarget.length; i++) {
		if(!(elementsToTarget[i].classList.contains(cls))){
			elementsToTarget[i].classList.add(cls)
		}
	}
}
// used in toggleDropsAndAreas() & archive.twig
function removeClassFromElements(ele, cls){
	var elementsToTarget = document.querySelectorAll(ele);
	for (var i = 0; i < elementsToTarget.length; i++) {
		if(elementsToTarget[i].classList.contains(cls)){
			elementsToTarget[i].classList.remove(cls)
		}
	}
}
// used in archive.twig
function ifArrayContainsCheckedItem(theNodes){
	for (var i = 0; i < theNodes.length; i++) {
		if(theNodes[i].checked){
			return true;
		}
	}
	return false;
}

//
// general helpers
//

// helper for checking for node changes within the given element/s & fire the given callback when changes occur
// https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver
function whenNodesChange(selector, observer_callback) {

	// Select the node that will be observed for mutations
	const targetNode = document.querySelector(selector);

	// Options for the observer (which mutations to observe)
	const config = { attributes: false, childList: true, subtree: true };

	// Callback function to execute when mutations are observed
	const callback = function (mutationList, observer) {
		// Use traditional 'for loops' for IE 11
		for (const mutation of mutationList) {
			if (mutation) {
				// console.log('Something has changed');
				observer_callback();
			}
			// if (mutation.type === 'childList') {
			//     console.log('A child node has been added or removed.');
			// }
			// else if (mutation.type === 'attributes') {
			//     console.log('The ' + mutation.attributeName + ' attribute was modified.');
			// }
		}
	};

	// Create an observer instance linked to the callback function
	const observer = new MutationObserver(callback);

	// Start observing the target node for configured mutations
	observer.observe(targetNode, config);

	// Later, you can stop observing
	// observer.disconnect();

}

//
// named helpers
//

// website preloader animation
preloadMe = function() {
	document.getElementById("Preloader").classList.add("hidden");
	document.getElementsByTagName("BODY")[0].classList.remove("no-overflow");
}
// when using an absolutely top-positioned site Header, we can use this to add margin to the top of the Page header content to compensate
headerSize = function() {
	var element = document.getElementById("SiteHeader");
	let computedStyle = getComputedStyle(element);
	var height = computedStyle.height;
	// Add the integer values of the left values together
	var new_height = parseInt( height, 10 ) + parseInt( "20px", 10 ) + "px";
	document.getElementById("PageHeaderWrap").style.paddingTop = new_height;
}
// dealing with mailchimp4wp's checkboxes for forms. this needs firing on document.ready downstream
mailchimp4WpStyles = function() {
	jQuery(function($) {
		$(".mc4wp-checkbox input").addClass("uk-checkbox uk-margin-small-right");
		$(".mc4wp-checkbox label").addClass("uk-margin-small-top uk-width-large");
	});
}

//
// old quickload function. requires reEnableDisabledCheckboxes()
//

// ajax loads (fetch api) for elements with data-link attributes (like pagination)
function addQuickLoadToDataLinkAttrs(){
	// console.log('hi there!');
	document.querySelector('main').addEventListener('click', function(event) {
		if (event.target.hasAttribute('data-link')) {

			// prevent default submit event
			event.preventDefault();

			// 1. set var for 'data_link', the data-link attribute we need the value of (should be a valid url)
			var data_link = event.target.getAttribute("data-link");

			// document.getElementById('DemoPostsGrid').classList.remove("uk-hidden");
			// document.getElementById('PostsGrid').textContent = '';

			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			})

			// await delay(1000);

			document.getElementById('DemoPostsGrid').classList.remove("uk-hidden");
			document.getElementById('PostsGrid').textContent = '';

			fetch(data_link)
			.then((response) => {
				// document.getElementById('DemoPostsGrid').classList.remove("uk-hidden");
				// document.getElementById('PostsGrid').textContent = '';
				return response.text();
			})
			.then(function(html) { // then we...
				// console.log('success!!!');
				var parser = new DOMParser();
				var newDocObj = parser.parseFromString(html, 'text/html');
				var newContent = newDocObj.querySelector('#TheLoop');
				document.getElementById('DemoPostsGrid').classList.add("uk-hidden");
				document.getElementById("TheLoopContainer").replaceChild(newContent, document.querySelector('#TheLoop'));
				wooGlobalStyles();
				reEnableDisabledCheckboxes('FiltersFormDropArea');
				// console.log('the content has been replaced');
			})
			.catch(error => {
				// console.error('Somethings gone wrong...', error);
			});

		};
	});
}
