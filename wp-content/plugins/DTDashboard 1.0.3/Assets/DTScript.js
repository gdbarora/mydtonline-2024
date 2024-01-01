jQuery(document).ready(function ($) {
    var isLoaded = false;

    // Function to create the popup
    function createPopup(widgetTitle, sortby=null, order=null) {
        widgetId = widgetTitle.toLowerCase().replace(/\s+/g, '-');
        // Create the popup container
        var popup = document.createElement('div');
        popup.classList.add('popup');
        document.body.appendChild(popup);


        // Create the header
        var header = document.createElement('div');
        header.classList.add('header');
        header.innerHTML = widgetTitle;
        popup.appendChild(header);
        // Create the close button
        var closeButton = document.createElement('span');
        closeButton.classList.add('close');
        closeButton.innerHTML = '&times;';
        closeButton.onclick = function () {
            document.body.removeChild(popup);
            isLoaded = false;
            document.getElementById('wpwrap').style.display = '';
        };
        header.appendChild(closeButton);
        // Create the control panel container
        var controlPanel = document.createElement('div');
        controlPanel.classList.add('control-panel');
        popup.appendChild(controlPanel);

        // Create the table container
        var tableContainer = document.createElement('div');
        tableContainer.classList.add('table-container');
        popup.appendChild(tableContainer);
        var loadingsk = document.createElement('div');
        loadingsk.classList.add('content');
        loadingsk.id = "loading-skeleton"
        popup.appendChild(loadingsk);
        loadingsk.innerHTML = `
        <div class="bars">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>
        <div class="bars">
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
          <div class="bar"></div>
        </div>`;
        document.getElementById('wpwrap').style.display = 'none';
        // You can create additional functions to generate fields and tables here
        // Example:



        // Display the popup
        popup.style.display = 'block';
        createFields(widgetTitle, controlPanel, tableContainer, sortby, order);
    }

    // Example function to create fields
    function createFields(widgetTitle, container, tableContainer, sortby=null, order=null) {
        if (widgetTitle === "Member Growth") {
            // Create a select element for the year
            var yearSelect = document.createElement('select');
            yearSelect.id = 'yearSelector';

            // Create a label for the year select element
            var yearLabel = document.createElement('label');
            yearLabel.textContent = 'Select Year: ';
            yearLabel.setAttribute('for', 'yearSelector');

            // Get the current year
            var currentYear = new Date().getFullYear();

            // Create options for the select element from 2020 to the current year
            for (var year = currentYear; year >= 2020; year--) {
                var option = document.createElement('option');
                option.value = year;
                option.text = year;
                yearSelect.appendChild(option);
            }

            // Append the label and year selector to the container
            container.appendChild(yearLabel);
            container.appendChild(yearSelect);
            var selectedYear = yearSelect.value; // Access the yearSelect
            yearSelect.addEventListener("change", function () {
                var selectedYear = yearSelect.value; // Access the yearSelect variable within the closure
                updateTable(widgetTitle, tableContainer, selectedYear);
            });
        } else if (widgetTitle === "Member Location" || widgetTitle === "Member Languages") {
            // Create two date input elements for "From" and "To" dates
            var fromDateInput = document.createElement('input');
            fromDateInput.type = 'date';
            fromDateInput.id = 'fromDate';

            var toDateInput = document.createElement('input');
            toDateInput.type = 'date';
            toDateInput.id = 'toDate';

            // Create labels for the date input elements
            var fromDateLabel = document.createElement('label');
            fromDateLabel.textContent = 'From: ';
            fromDateLabel.setAttribute('for', 'fromDate');

            var toDateLabel = document.createElement('label');
            toDateLabel.textContent = 'To: ';
            toDateLabel.setAttribute('for', 'toDate');

            // Append the labels and date input elements to the container
            container.appendChild(fromDateLabel);
            container.appendChild(fromDateInput);
            container.appendChild(toDateLabel);
            container.appendChild(toDateInput);

            // Add event listeners to both date input elements
            fromDateInput.addEventListener("change", function () {
                var fromDate = fromDateInput.value; // Get the selected "From" date
                var toDate = toDateInput.value; // Get the selected "To" date
                updateTable(widgetTitle, tableContainer, undefined, fromDate, toDate);
            });

            toDateInput.addEventListener("change", function () {
                var fromDate = fromDateInput.value; // Get the selected "From" date
                var toDate = toDateInput.value; // Get the selected "To" date
                updateTable(widgetTitle, tableContainer, undefined, fromDate, toDate);
            });
        }
        createTable(widgetTitle, tableContainer, selectedYear, sortby, order);
    };




    function createTable(widgetTitle, container, selectedYear = null, sortby = null, order = null) {
        widgetId = widgetTitle.toLowerCase().replace(/\s+/g, '-');
    
        $.ajax({
            url: ajaxUrl.ajax_url,
            method: "POST",
            data: {
                action: 'table_content',
                widgetId: widgetId,
                selectedYear: selectedYear,
            },
            success: function (response) {
                isLoaded = true;
                document.getElementById('loading-skeleton').style.display = 'none';
                container.innerHTML = response;
    
                // Check if widgetTitle is "Member Growth" and disable sorting accordingly
                var isSortable = widgetTitle !== "Member Growth";
    
                var dataTableOptions = {
                    paging: isSortable,
                    lengthChange: isSortable,
                    lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'All']],
                    pageLength: 25,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'collection',
                            text: 'Export',
                            buttons: [
                                'copy', 'csv', 'excel', 'print'
                            ],
                            fade: true
                        }
                    ]
                };
				if (!isSortable) {
					// Disable initial sorting when isSortable is false
					dataTableOptions.order = [];
				}
    
                if (sortby !== null && order !== null) {
                    // Find the column index by the header text (column title)
                    var columnIndex = $(container).find('thead th:contains("' + sortby + '")').index();
    
                    if (columnIndex >= 0) {
                        // Sort by the specified column in the specified order
                        dataTableOptions.order = [[columnIndex-1, order]];
                    }
                }
    
                let table = $(container).find('table').DataTable(dataTableOptions);
				// Create a tfoot element if it doesn't exist
				if (!$(container).find('table tfoot').length) {
					$(container).find('table').append('<tfoot><tr></tr></tfoot>');
					$(container).find('table thead th').each(function (index) {
					let title = $(this).text();
					$(container).find('table tfoot tr').eq(0).append('<th><input type="text" placeholder="' + title + '" /></th>');
				});
				}
				


				// Enable DataTables search for each column
				$(container).find('table tfoot tr input').on('keyup change', function () {
					var columnIndex = $(this).closest('th').index();
					table.column(columnIndex).search(this.value).draw();
				});
            },
            error: function (error) {
                console.error("Error fetching table data: " + error.statusText);
            },
        });
    }
    

    function updateTable(widgetTitle, container, selectedYear = undefined, fromDate = undefined, toDate = undefined) {
        // Show loading skeleton initially
        document.getElementById('loading-skeleton').style.display = 'flex';
        document.getElementsByClassName('table-container')[0].innerHTML = '';
        widgetId = widgetTitle.toLowerCase().replace(/\s+/g, '-');

        // Create the data object to send in the AJAX request
        var requestData = {
            action: 'table_content',
            widgetId: widgetId,
        };

        // Include selectedYear in the data if defined
        if (selectedYear !== undefined) {
            requestData.selectedYear = selectedYear;
        }

        // Include fromDate and toDate in the data if defined
        if (fromDate !== undefined && toDate !== undefined) {
            requestData.fromDate = fromDate;
            requestData.toDate = toDate;
        }

        $.ajax({
            url: ajaxUrl.ajax_url,
            method: "POST",
            data: requestData,
            success: function (response) {
                isLoaded = true;
                document.getElementById('loading-skeleton').style.display = 'none';
                container.innerHTML = response;
                var isMemberGrowth = widgetTitle !== "Member Growth";

                $(container).find('table').DataTable({
                    // DataTables configuration options go here
                    "paging": isMemberGrowth,
                    "ordering": isMemberGrowth, // Set sorting based on the condition
                    "lengthChange": isMemberGrowth,
                });
            },
            error: function (error) {
                console.error("Error fetching table data: " + error.statusText);
            },
        });
    }



    // Add a click event handler for all widget headers.
    $('.postbox-header h2').on('click', function () {
        var widgetId = $(this).closest('.postbox').attr('id');
        var widgetTitle = $(this).text();
        createPopup(widgetTitle);

    });


    // Select all elements with the class 'postbox'
    var postboxes = $('.postbox');

    // Iterate through each 'postbox' element
    postboxes.each(function () {
        updateWidgetData($(this));
    });

    // Function to update widget data
    function updateWidgetData(postbox) {
        var widgetId = postbox.attr('id'); // Get the widget ID from the 'id' attribute

        var yearSelector = postbox.find('#year'); // Find the year selector within the current postbox
		var startDateInput = postbox.find('#start_date'); // Find the start date input
    	var endDateInput = postbox.find('#end_date'); // Find the end date input
        // Function to perform the AJAX request and update the widget content
        function performAjaxRequest(selectedYear=undefined, startDateInput=undefined, endDateInput=undefined) {
            $.ajax({
                url: ajaxUrl.ajax_url,
                type: 'POST',
                data: {
                    action: 'widget_content',
                    widgetId: widgetId,
                    selectedYear: selectedYear,
					fromDate: startDateInput,
					toDate: endDateInput,
                },
                beforeSend: function () {
                    $('#' + widgetId + '-content').html(`<div class="content">
                <div class="bars">
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                </div>
                <div class="bars">
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                  <div class="bar"></div>
                </div>
              </div>`);
                },
                success: function (data) {
                    var response = JSON.parse(data);
                    var htmlContent = response.html_content;

                    // Update the content of the specified widget and its child container
                    $('#' + widgetId + '-content').html(htmlContent);
                    if (widgetId === 'groups') {
                        $('#groups #groups-content').find('.group-type-title').on('click', function () {
                            var widgetId = $(this).closest('.postbox').attr('id');
                            var widgetTitle = $(this).closest('.postbox').find('h2').text();
                            var clickedHeading = $(this).text();
                            if(clickedHeading==="Largest Groups"){
                                sortby = 'Population';
                                order = 'desc';
                            }
                            else if(clickedHeading==="Smallest Groups"){
                                sortby = 'Population';
                                order = 'asc';
                            }
                            else if(clickedHeading==="Quietest Groups"){
                                sortby = 'Active Members';
                                order = 'asc';
                            }
                            else if(clickedHeading==="Busiest Groups"){
                                sortby = 'Active Members';
                                order = 'desc';
                            }
                            createPopup(widgetTitle, sortby, order);
                        });
                    }
                },
                error: function (error) {
                    console.error('Error fetching widget content: ' + error.statusText);
                }
            });
        }

        // Add an event listener to the year selector
        yearSelector.on('change', function () {
            var selectedYear = yearSelector.val(); // Get the selected year
            performAjaxRequest(selectedYear); // Perform the AJAX request
        });
		startDateInput.on('change', function () {
			var startDate = startDateInput.val();
			var endDate = endDateInput.val();
			performAjaxRequest(undefined, startDate, endDate);
		});

		endDateInput.on('change', function () {
			var startDate = startDateInput.val();
			var endDate = endDateInput.val();
			performAjaxRequest(undefined, startDate, endDate);
		});

        // Initially perform the AJAX request with the current year value
        var initialSelectedYear = yearSelector.val();
        performAjaxRequest(initialSelectedYear);
    }
});





jQuery(document).ready(function($) {
    // Add click event handler for the "Toggle panel" icon
    $('.handlediv').on('click', function() {
      $(this).closest('.postbox').find('.inside').toggle();
        $(this).closest('.postbox').toggleClass('closed');// Toggle the visibility of the div
    });
  });
