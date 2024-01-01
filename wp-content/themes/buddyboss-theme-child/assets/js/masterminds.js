jQuery(document).ready(function ($) {

	function addLink() {
		const linkInput = document.querySelector('[name="links"]');
		const link = linkInput.value;

		if (link) {
			updateLinksDisplay(link);
			linkInput.value = '';
		}
	}
	// Select the "Add Link" button by its id
	const addLinkButton = document.getElementById('addLinkButton');

	// Add an event listener to the button
	addLinkButton.addEventListener('click', function () {
		addLink(this); // Call the addLink function when the button is clicked
	});

	function updateLinksDisplay(link) {
		const linksContainer = document.getElementById('dt-links-box');
		const linkBox = document.createElement('div');
		linkBox.classList.add('link-box');
		linkBox.innerHTML = `<a href="${link}" target="_blank">${link}</a><span class="remove-link">x</span>`;

		// Add an event listener to the "Remove Link" button within the newly created link box
		const removeLinkButton = linkBox.querySelector('.remove-link');
		removeLinkButton.addEventListener('click', function () {
			// Identify the parent link box and remove it from the container
			linksContainer.removeChild(linkBox);
		});

		linksContainer.appendChild(linkBox);
	}
	function updateDescription(videoBeingEdited) {
		const data = {
			Title: document.getElementById('dtstature-title').value,
			Topic: document.getElementById('dtstature-topic').value,
			'6A-Journey': document.getElementById('dtstature-journey').value,
			Tags: [], // Initialize an empty array for tags
			'Additional Links': [], // Initialize an empty array for links
		};
		const vimeoId = document.getElementById('vimeo-video-id').value;

		const tagsInput = document.getElementById('dtstature-tags');
		const tagsValue = tagsInput.value;
		if (tagsValue) {
			data.Tags = tagsValue.split(',').map(tag => tag.trim());
		}

		// Get all the link boxes
		const linkBoxes = document.querySelectorAll('.link-box');

		// Iterate through the link boxes and extract the links
		linkBoxes.forEach(linkBox => {
			const linkElement = linkBox.querySelector('a');
			if (linkElement) {
				const link = linkElement.getAttribute('href');
				data['Additional Links'].push(link);
			}
		});

		// Convert the data object to JSON and log it to the console
		let newDescription = JSON.stringify(data, null, 2);
		//console.log(newDescription);
		updateVideoDescription(vimeoId, newDescription, videoBeingEdited);
	}

	function updateVideoDetails(description, videoBeingEditedId) {
		let videoBeingEdited = document.getElementById(videoBeingEditedId);
		//console.log(videoBeingEdited);
		let mastermindMeta = JSON.parse(description.replace(/\\/g, ''));

		if (mastermindMeta) {
			mastermindTitle = mastermindMeta['Title'];
			mastermindJourney = mastermindMeta['6A-Journey'];
			mastermindTopic = mastermindMeta['Topic'];
			mastermindTags = mastermindMeta['Tags'];
			mastermindLinks = mastermindMeta['Additional Links'];
		}
		if(mastermindTitle){
			const titleDiv = videoBeingEdited.querySelector(".mastermind-title-desc h3");
			if (titleDiv) {
				titleDiv.innerHTML =  mastermindTitle;
			}
		}else{
			const titleDiv = videoBeingEdited.querySelector(".mastermind-title-desc h3");
			titleDiv.innerHTML = titleDiv.getAttribute('data-created');
		}

		// Update the existing elements with the new data
		if (mastermindTopic) {
			videoBeingEdited.setAttribute("data-topic", mastermindTopic);
		}

		if (mastermindJourney) {
			// Assuming there's an element with the class "journey" within videoBeingEdited
			const journeyDiv = videoBeingEdited.querySelector(".journey");
			if (journeyDiv) {
				journeyDiv.innerHTML = "<strong>6A Journey: </strong>" + mastermindJourney;
			}
		}
		if (mastermindTopic) {
			let topicDiv = videoBeingEdited.querySelector(".mastermind-topic");
			topicDiv.innerHTML = "<strong>Topic: </strong>" + mastermindTopic;
		}

		// Update additional links
		const additionalLinksDiv = videoBeingEdited.querySelector(".additional-links");
		if (additionalLinksDiv) {
			additionalLinksDiv.innerHTML = ""; // Clear the existing links

			if (mastermindLinks) {
				mastermindLinks.forEach((mastermindLink, index) => {
					const li = document.createElement("li");
					const a = document.createElement("a");
					a.textContent = mastermindLink;
					a.href = mastermindLink;
					a.target = "_blank"; // Open in a new tab
					li.appendChild(a);
					additionalLinksDiv.appendChild(li);
				});
			}
		}
	}

	function updateVideoDescription(videoId, newDescription, videoBeingEdited) {
		$.ajax({
			type: 'POST',
			url: ajaxurl, // WordPress AJAX URL
			data: {
				action: 'update_video_description', // WordPress action hook
				video_id: videoId,
				new_description: newDescription
			},
			beforeSend: function() {
				document.getElementById('mastermind-overlay').style.display = 'block';
			},
			success: function (response) {
				document.getElementById('mastermind-overlay').style.display = '';

				updateVideoDetails(response.description, videoBeingEdited);
				// You can perform additional actions here based on the response
			},
			error: function (xhr, status, error) {
				console.error(error);
				alert('Video details could not be updated.');
				document.getElementById('mastermind-overlay').style.display = '';

				// Handle any errors here
			}
		});
	}

	function videoVisibilityToggler(vimeoVideoId, vimeoAction, clickedButton){
		$.ajax({
			type: 'POST',
			url: ajaxurl, // WordPress AJAX URL
			data: {
				action: 'video_visibility_toggler_mastermind', // WordPress action hook
				video_id: vimeoVideoId,
				vimeoAction: vimeoAction,
			},
			success: function (response) {
				if(response.success){
					let responseData = response.data;
					if(responseData.isHidden){
						clickedButton.textContent = "Show Mastermind";
						clickedButton.setAttribute('data-toggle-video', 'DELETE');
					}else{
						clickedButton.textContent = "Hide Mastermind";
						clickedButton.setAttribute('data-toggle-video', 'PUT');
					}
				}
			},
			error: function (xhr, status, error) {
				console.error(error);
				alert('Video details could not be updated.');
			}
		});
	}

	function getPreviousVimeoDetails(vimeoId, editModal){
		$.ajax({
			type: 'POST',
			url: ajaxurl, // WordPress AJAX URL
			data: {
				action: 'get_video_description', // WordPress action hook
				video_id: vimeoId,
			},
			success: function (response) {
				console.log(response);
				editModal.querySelector('form').classList.remove('dt-hidden');
				let mastermindMeta = JSON.parse(response.replace(/\\/g, ''));

				if (mastermindMeta) {
					mastermindTitle = mastermindMeta['Title'];
					mastermindJourney = mastermindMeta['6A-Journey'];
					mastermindTopic = mastermindMeta['Topic'];
					mastermindTags = mastermindMeta['Tags'];
					mastermindLinks = mastermindMeta['Additional Links'];
				}
				// Assuming mastermindMeta is an object with properties like 'Title', '6A-Journey', 'Topic', 'Tags', 'Additional Links'
				if (mastermindMeta) {
					document.getElementById('dtstature-title').value = mastermindMeta['Title'] || '';
					document.getElementById('dtstature-journey').value = mastermindMeta['6A-Journey'] || '';
					document.getElementById('dtstature-topic').value = mastermindMeta['Topic'] || '';
					document.getElementById('dtstature-tags').value = (mastermindMeta['Tags'] || []).join(', ');

					// Assuming mastermindMeta is an object with 'Additional Links' as an array
					if (mastermindMeta && mastermindMeta['Additional Links'] && mastermindMeta['Additional Links'].length > 0) {
						var linksContainer = document.getElementById('dt-links-box');

						mastermindMeta['Additional Links'].forEach(function (link) {
							// Create a new link box
							var linkBox = document.createElement('div');
							linkBox.className = 'link-box';

							// Create a link element
							var linkElement = document.createElement('a');
							linkElement.href = link;
							linkElement.target = '_blank';
							linkElement.textContent = link;

							// Create a remove link span
							var removeLinkSpan = document.createElement('span');
							removeLinkSpan.className = 'remove-link';
							removeLinkSpan.textContent = 'x';

							// Append link and remove link span to link box
							linkBox.appendChild(linkElement);
							linkBox.appendChild(removeLinkSpan);

							// Append the link box to the container
							linksContainer.appendChild(linkBox);

							// Add an event listener to remove the link box when 'x' is clicked
							removeLinkSpan.addEventListener('click', function () {
								linksContainer.removeChild(linkBox);
							});
						});
					}

				}

			},
			error: function (xhr, status, error) {
				console.error(error);
				alert('Video details could not be updated.');
				// Handle any errors here
			}
		});

	};


	// Select the "Update Data" button by its id
	const updateDataButton = document.getElementById('updateDescriptionButton');

	updateDataButton.addEventListener('click', function (event) {
		const videoBeingEdited = document.getElementById('video-being-edited').value;
		updateDescription(videoBeingEdited);
		closeEditVideoModal();
	});


	function closeEditVideoModal() {
		// Add the "dt-hidden" class to the edit modal
		const editModal = document.getElementById('editVideoModal');
		editModal.classList.add('dt-hidden');

		// Assuming you have a form with the ID "dtstature-jsonForm"
		const form = document.getElementById('dtstature-jsonForm');
		form.classList.add('dt-hidden');

		document.getElementById('dt-links-box').innerHTML = '';

		// Reset the form
		form.reset();

	}

	// Select the "Close" button by its id
	const closeEditButton = document.getElementById('closeEditButton');

	// Add an event listener to the button
	closeEditButton.addEventListener('click', closeEditVideoModal);




	function fetchMastermindData(folder_id, order = 'desc', page = '1') {
		let sort_by = 'date';

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'get_selected_mastermind',
				folder_id: folder_id,
				sort_by: sort_by,
				order: order,
				page: page,
			}, beforeSend: function () {
				$('#mastermind-replay').html('').addClass('loading-skeleton');
				$('#mastermind-topic-filter').html('<option value="0">Filter by Topic</option>');
				$('#mastermind-replay-list').html('');
				$('#mastermind-pagination').html('');

				for (let i = 0; i < 10; i++) {
					$('#mastermind-replay-list').append('<li class="mastermind-replay loading-skeleton"></li>');
				}

			},
			success: function (response) {
				$('#mastermind-replay-list').html('');
				$('#mastermind-replay').removeClass('loading-skeleton');
				if(response.data){
					updateVimeoList(response.data);
				}
				else if(response.error){
					alert('Selected Mastermind Couln\'t be found.');
				}
				if (response.paging.previous !== response.paging.next) {
					setPagination(response.paging);
				}
			},
			error: function (response) {
				// Handle any errors.
				console.error('AJAX error:', response);
			}
		});
	}


	function updateIframe(iframeHTML) {
		$('#all-masterminds #mastermind-replay').html(iframeHTML);

		// Get the height of the viewport
		const viewportHeight = $(window).height();

		// Get the top position of the #mastermind-replay container
		const containerTop = $('#mastermind-replay').offset().top;

		// Calculate the scroll position to center the container
		const scrollTo = containerTop - (viewportHeight / 2);

		// Scroll to the calculated position
		$('html, body').animate({
			scrollTop: scrollTo
		}, 1000); // You can adjust the duration (1000ms = 1 second) to your preference
	}





	function updateVimeoList(videosArray) {
		let firstVideo = true;
		let count = 1;
		let mastermindsContainer = document.getElementById("mastermind-replay-list");
		let selectTopic = document.getElementById('mastermind-topic-filter');
		selectTopic.innerHTML = '<option value="0">Filter by Topic</option>';

		let selectJourney = document.getElementById('mastermind-6ajourney-filter');
		selectJourney.innerHTML = '<option value="0">Filter by 6A Journey</option>';

		// Add event listeners to the filter selects
		selectTopic.addEventListener('change', filterListItems);
		selectJourney.addEventListener('change', filterListItems);

		videosArray.forEach(video => {
			let mastermindVimeoId = (video['uri']).split('/').pop();

			if (firstVideo) {
				let embedHTML = video['embed']['html'];
				updateIframe(embedHTML);
			}

			let embedHTML = video['embed']['html'];
			let mastermindMeta;

			try {
				mastermindMeta = JSON.parse((video['description']).replace(/\\/g, ''));
			} catch (error) {
				mastermindMeta = undefined;
			}


			let mastermindThumbnail = video['pictures']['base_link'] + "_200x150?r=pad";
			let mastermindCreated = new Date(video['created_time']);
			let mastermindTitle = formatDate(mastermindCreated, 'dd Month yyyy');
			let mastermindJourney = '';
			let mastermindTopic = '';
			let mastermindTags = [];
			let mastermindLinks = [];
			let mastermindVimeoTags = video['tags'];
			const isMastermindHidden = mastermindVimeoTags.some(function(tag) {
				return tag.canonical === 'hidemastermind';
			});



			if (mastermindMeta) {
				mastermindTitle = mastermindMeta['Title']?  mastermindMeta['Title'] : mastermindTitle;
				mastermindJourney = mastermindMeta['6A-Journey'];
				mastermindTopic = mastermindMeta['Topic'];
				mastermindTags = mastermindMeta['Tags'];
				mastermindLinks = mastermindMeta['Additional Links'];
			}
			if (mastermindTopic) {
				const selectTopic = document.getElementById("mastermind-topic-filter");

				// Check if an option with the same value already exists
				if (!selectTopic.querySelector(`option[value="${mastermindTopic}"]`)) {
					const option = document.createElement("option");
					option.value = mastermindTopic;
					option.textContent = mastermindTopic;

					selectTopic.appendChild(option);
					sortOptionsAlphabetically(selectTopic, 1);
				}
			}


			if (mastermindJourney) {
				const selectJourney = document.getElementById("mastermind-6ajourney-filter");

				if(!selectJourney.querySelector(`option[value="${mastermindJourney}"]`)) {
					const option = document.createElement("option");
					option.value = mastermindJourney; // You can use a unique value here
					option.textContent = mastermindJourney;

					selectJourney.appendChild(option);
					sortOptionsAlphabetically(selectJourney, 1);
				}
			}



			let listItem = document.createElement("li");
			listItem.id = "masteremind-replay-" + count;
			listItem.className = "mastermind-replay";
			listItem.setAttribute("data-embedHtml", embedHTML);
			listItem.setAttribute("data-topic", mastermindTopic)

			let thumbnailDiv = document.createElement("div");
			thumbnailDiv.className = "mastermind-thumbnail";
			let thumbnailImage = document.createElement("img");
			thumbnailImage.src = mastermindThumbnail;
			thumbnailDiv.appendChild(thumbnailImage);

			let titleDescDiv = document.createElement("div");
			titleDescDiv.className = "mastermind-title-desc";

			let title = document.createElement("h3");
			title.textContent = mastermindTitle;
			title.setAttribute('data-created', formatDate(mastermindCreated, 'dd Month yyyy'));

			let createdDate = document.createElement("div");
			createdDate.className = "created-date";
			createdDate.textContent = formatDate(mastermindCreated, 'dd/mm/yyyy');

			let topicDiv = document.createElement("div");
			topicDiv.className = "mastermind-topic";
			topicDiv.innerHTML = "<strong>Topic: </strong>" + mastermindTopic;

			let journeyDiv = document.createElement("div");
			journeyDiv.className = "journey";
			journeyDiv.innerHTML = "<strong>6A Journey: </strong>" + mastermindJourney;

			titleDescDiv.appendChild(title);
			//titleDescDiv.appendChild(createdDate); //Commented as Per client requirement
			titleDescDiv.appendChild(topicDiv);
			titleDescDiv.appendChild(journeyDiv);

			let additionalLinksDiv = document.createElement("div");
			additionalLinksDiv.className = "additional-links-container";
			let h2 = document.createElement("h4");
			h2.textContent = "Files & Links";
			additionalLinksDiv.appendChild(h2);
			let ul = document.createElement("ul");
			ul.className = "additional-links";
			mastermindLinks.forEach((mastermindLink, index) => {
				const li = document.createElement("li");
				const a = document.createElement("a");
				a.textContent = mastermindLink;
				a.href = mastermindLink;
				a.target = "_blank"; // Open in a new tab
				li.appendChild(a);



				ul.appendChild(li);
			});
			additionalLinksDiv.appendChild(ul);

			listItem.appendChild(thumbnailDiv);
			listItem.appendChild(titleDescDiv);
			listItem.appendChild(additionalLinksDiv);

			if (bb_pusher_vars.is_admin === 'yes') {
				// Check if the current user is an admin
				let editButton = document.createElement("button");
				editButton.textContent = "Edit Video Details";
				editButton.className = "edit-video-button";

				// Set the data attribute for vimeo_video_id
				editButton.setAttribute('data-vimeo-video-id', mastermindVimeoId); // Replace with the actual vimeo_video_id

				// Add an event listener to open the modal and populate it with data
				editButton.addEventListener("click", function (event) {
					let confirmed = confirm("Are you sure you want to update this video details?");
					if (!confirmed) { return; }
					const editModal = document.getElementById('editVideoModal');
					editModal.classList.remove('dt-hidden');

					const clickedButton = event.target;
					const videoBeingEdited = clickedButton.parentElement.id;
					const vimeoVideoId = editButton.getAttribute('data-vimeo-video-id');

					getPreviousVimeoDetails(vimeoVideoId, editModal);

					document.getElementById('vimeo-video-id').value = vimeoVideoId;
					document.getElementById('video-being-edited').value = videoBeingEdited;
					var clonedContent = document.getElementById(videoBeingEdited).cloneNode(true);
					clonedContent.querySelector('.edit-video-button').remove();
					clonedContent.querySelector('.toggle-video-button').remove();
					clonedContent.querySelector('.additional-links-container').remove();
					document.getElementById('dt-video-container').innerHTML = clonedContent.innerHTML;
				});

				let toggleVisibilityButton = document.createElement("button");
				if(isMastermindHidden){
					toggleVisibilityButton.textContent = "Show Mastermind";
					toggleVisibilityButton.setAttribute('data-toggle-video', 'DELETE');
				}else{
					toggleVisibilityButton.textContent = "Hide Mastermind";
					toggleVisibilityButton.setAttribute('data-toggle-video', 'PUT');
				}
				toggleVisibilityButton.className = "toggle-video-button";
				toggleVisibilityButton.setAttribute('data-vimeo-video-id', mastermindVimeoId); // Replace with the actual vimeo_video_id



				// Add an event listener to open the modal and populate it with data
				toggleVisibilityButton.addEventListener("click", function (event) {
					let confirmed = confirm("Are you sure you want to toggle visibility of this video from Masterminds Page?");
					if (confirmed){

						const vimeoVideoId = toggleVisibilityButton.getAttribute('data-vimeo-video-id');
						const vimeoAction = toggleVisibilityButton.getAttribute('data-toggle-video');
						const clickedButton = event.target;
						videoVisibilityToggler(vimeoVideoId, vimeoAction, clickedButton);}
					else{
						return;
					}
				});



				listItem.appendChild(editButton);
				listItem.appendChild(toggleVisibilityButton);
			}


			mastermindsContainer.appendChild(listItem);
			firstVideo = false;
			count++;


		});

		function filterListItems() {
			let selectedTopic = selectTopic.value;
			let selectedJourney = selectJourney.value;
			let listItems = document.querySelectorAll('#mastermind-replay-list li');

			listItems.forEach(item => {
				const itemTopic = item.getAttribute('data-topic');
				const journeyDiv = item.querySelector('.journey');
				const itemJourney = journeyDiv ? journeyDiv.textContent.replace('6A Journey: ', '') : '';

				if (
					(selectedTopic === '0' || itemTopic === selectedTopic) &&
					(selectedJourney === '0' || itemJourney === selectedJourney)
				) {
					item.style.display = 'grid'; // Show the item
				} else {
					item.style.display = 'none'; // Hide the item
				}
			});
		}

	}


	// Function to filter list items based on selected values



	function updateMastermindContent() {
		let activeTab = $('#all-masterminds .tablinks.active');
		let mastermindHeading = activeTab.text();
		let mastermindDesc = activeTab.data('desc');

		let mastermindHeadingBox = $('h2#mastermind-heading');
		let mastermindDescBox = $('#mastermind-description');

		mastermindHeadingBox.html(mastermindHeading);
		mastermindDescBox.html(mastermindDesc);

	}

	function handleMastermindTabClick() {
		const tabLinks = document.querySelectorAll('.tablinks');
		tabLinks.forEach((tabLink) => {
			tabLink.addEventListener('click', () => {
				let availableFilters = tabLink.getAttribute('available_filters');
				let filtersArray = availableFilters.split(',');
				console.log(filtersArray);
				// Iterate through all select elements
				document.querySelectorAll('select').forEach(select => {
					if (!filtersArray.includes(select.id) && select.id !== "mastermind-year-filter") {
						console.log(select.id);

						select.style.display = 'none';
					} else {
						select.style.display = '';
					}
				});


				let yearsFilter = document.getElementById('mastermind-year-filter');
				yearsFilter.innerHTML = tabLink.getAttribute('data-years');
				// Check if the tabLink doesn't contain the 'active' class
				if (!tabLink.classList.contains('active')) {
					tabLinks.forEach((link) => link.classList.remove('active'));
					tabLink.classList.add('active');
					updateMastermindPage(tabLink);
				}
			});
		});
	}


	function updateMastermindPage(tabLink, order = 'desc', page = 1) {
		let folder_id = tabLink.getAttribute('folder_id');
		fetchMastermindData(folder_id, order, page);
		updateMastermindContent();
	}

	function handleMastermindItemClick() {
		let itemContainer = $('#mastermind-replay-list');
		itemContainer.on('click', 'li.mastermind-replay:not(li.mastermind-replay button):not(li.mastermind-replay a)', function () {
			var embedHTML = $(this).data('embedhtml');
			updateIframe(embedHTML);
		});


	}

	function setPagination(paginationData) {

		// Extract the page numbers
		const nextPage = getPageNumber(paginationData.next);
		const previousPage = getPageNumber(paginationData.previous);
		const firstPage = getPageNumber(paginationData.first);
		const lastPage = getPageNumber(paginationData.last);

		// Create HTML links or buttons for pagination with data-page attribute
		const paginationHtml = [];
		if (firstPage) {
			paginationHtml.push(`<a href="#" data-page="${firstPage}">First</a>`);
		}

		for (var i = 1; i <= lastPage; i++) {
			paginationHtml.push(`<a class="dt-paging" href="#" data-page="${i}">${i}</a>`);
		}


		if (lastPage) {
			paginationHtml.push(`<a href="#" data-page="${lastPage}">Last</a>`);
		}

		// Join the links and display them in your front-end
		const paginationElement = document.getElementById('mastermind-pagination');
		paginationElement.innerHTML = paginationHtml.join(' ');

		// Add event listeners for pagination
		const paginationLinks = paginationElement.getElementsByTagName('a');
		for (const link of paginationLinks) {
			link.addEventListener('click', handlePaginationClick);
		}

		// Function to extract page number from URL
		function getPageNumber(url) {
			if (!url) return null;
			const match = url.match(/page=(\d+)/);
			return match ? match[1] : null;
		}

		// Function to handle pagination click
		function handlePaginationClick(event) {
			event.preventDefault();
			const pageNumber = event.target.getAttribute('data-page');
			if (pageNumber) {
				let activeTab = $('#all-masterminds .tablinks.active')[0];
				updateMastermindPage(activeTab, 'desc', pageNumber);
			}
		}

	}

	function handleSortOrder() {
		let selectElement = document.getElementById("mastermind-sort-order");
		selectElement.addEventListener("change", function () {
			let activeTab = $('#all-masterminds .tablinks.active')[0];
			let order = selectElement.value;
			updateMastermindPage(activeTab, order);
		});
	}

	function formatDate(inputDate, outputFormat) {
		// Options for Pacific Standard Time (PST)
		var options = { timeZone: 'America/Los_Angeles', day: '2-digit', month: 'long', year: 'numeric' };
		// Format the date as a string in "dd Month yyyy" format with PST
		var formatter = new Intl.DateTimeFormat('en-US', options);
		var formattedDateString = formatter.format(inputDate);
		// Format the date as a string in PST
		// 		var pstDateString = inputDate.toLocaleDateString('en-US', options).replace(/(\w+) (\d+), (\d+)/, function(match, month, day, year) {
		//         return `${day} ${month} ${year}`;
		//     });

		return formattedDateString;

	}

	const yearFilter = document.getElementById('mastermind-year-filter');
	const tabLinks = document.querySelectorAll('.tablinks');

	// Attach an event listener to the select element
	yearFilter.addEventListener('change', handleYearsFilterChange);

	// Event handler function
	function handleYearsFilterChange() {
		// Get the selected option's value
		const selectedYear = yearFilter.value;

		// Update the folder_id in active tabLinks
		tabLinks.forEach((tabLink) => {
			if (tabLink.classList.contains('active')) {
				// Set the folder_id attribute to the selectedYear
				tabLink.setAttribute('folder_id', selectedYear);
				let selectElement = document.getElementById("mastermind-sort-order");
				let order = selectElement.value;
				updateMastermindPage(tabLink, order);
			}
		});
	}



	handleMastermindTabClick();
	handleMastermindItemClick();
	handleSortOrder();

	let activeTab = $('#all-masterminds .tablinks.active')[0];
	activeTab.click();
	updateMastermindPage(activeTab);

	function sortOptionsAlphabetically(selectElement, startIndex) {
		const options = Array.from(selectElement.options).slice(startIndex);
		options.sort((a, b) => a.textContent.localeCompare(b.textContent));

		// Add sorted options
		options.forEach(option => {
			selectElement.appendChild(option);
		});
	}


});