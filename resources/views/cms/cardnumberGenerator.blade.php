
<!--@extends('app')-->
@section('style')
<style>
    .result-container {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        height: 256.45px;

        background-color: #f5f5f5;
        overflow-y: auto;
    }
    .result-container2 {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        height: 256.45px;

        background-color: #f5f5f5;
        overflow-y: auto;
    }
    .image-spacing {
        margin-top: 10px; /* Adjust the value as needed */
        margin-left: 25px;
    }
    input:invalid + .error-message {
        display: block;
    }

    input:valid + .error-message {
        display: none;
    }

    input:placeholder-shown + .error-message {
        display: none;
    }



</style>
@endsection

@section('content')

<div class="container-fluid">
	<div class="navbar-fixed-top crumb" >
		<div class="col-menu-10">
			<div class="header-crumb">
				<ol class="breadcrumb" style=" border-radius:0; " >
                    <li class="active"><a href="{{ url(session('userBUCode').'/cms/cardnumber') }}" class="waiting">Generate Number<span class="badge" style="top:-9px; position:relative;"></span></a></li>
					<li class="active"><a></a><span class="badge" style="top:-9px; position:relative;"></span></a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="body-content row">
		<div class="col-menu-15 create-queue">
                <div class="panel-group" style="margin-top: 20px">
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="line-height: 12px;">Generate Number</div>
                        <div class="panel-body">
                            
                        <div id="alertMessage" class="alert alert-success" style="display: none;"></div>
                            
                            <div class="form-group col-md-4">
                                <div class="form-group">
                                    <label class="bold" for="year">Year (YYYY):</label>
                                    <input type="text" class="form-control" id="year" maxlength="4" required disabled>
                                </div>
                                <div class="form-group">
                                    <label class="bold" for="batch">Batch (XX):</label>
                                    <input type="text" class="form-control" id="batch" maxlength="2" required disabled>
                                </div>
                                <div class="form-group">
                                    <label class="bold" for="month">Month:</label>
                                    <select class="form-control" id="month" disabled required>
                                        <option disabled>-- Select Month --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="bold" for="series">How many numbers should be generated?</label>
                                    <input type="number" class="form-control" id="series" min="1" max="1000" required maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    
                                    <small class="error-message" style="color: red">Generated card number should not exceed 1000.</small>
                                </div>

                                <div class="form-group text-center">
                                    <button id="generate" type="button" class="btn btn-success" ><i class="fa fa-qrcode fa-lg"></i> Generate</button>
                                    <button id="history" type="button" class="btn btn-info" ><i class="fa fa-history"></i></button>
                                </div>
                            </div>

                            <form id="cardGenerator" class="form" action="{{ route('cardnumber.store') }}" method="POST">
                                @csrf
                                <div class="form-group col-md-4">
                                    <label class="bold text-center" for="month">Card Numbers</label>
                                        <div class="mt-3 result-container">
                                            <div id="results">
                                                <!-- Generated card numbers will be displayed here -->
                                                <div id="cardNumbers"></div>
                                            </div>
                                        </div>

                                        <div class="form-group text-center">
                                            <button id="savebtn" type="button" class="btn btn-primary" style="margin-top: 15px;" disabled><i class="fa fa-credit-card"></i> Save Card Numbers</button>
                                        </div>
                                </div>

                                <!-- Hidden input fields for year, batch, month, and seriesnum -->
                                <input type="hidden" name="year" id="yearInput" value="">
                                <input type="hidden" name="batch" id="batchInput" value="">
                                <input type="hidden" name="month" id="monthInput" value="">
                                <input type="hidden" name="seriesnum" id="seriesnumInput" value="">
                                <!-- Hidden input to store the card numbers -->
                                <input type="hidden" name="card_numbers[]" id="cardNumbersInput" value="">

                            </form>

                            <form id="saveBarcode" class="form" action="" method="POST">
                                @csrf
                                <div class="form-group col-md-4">
                                <label class="bold text-center" for="month">Barcodes</label>
                                    <div class="mt-3 result-container2">
                                        <div id="results2">
                                            <!-- Generated barcodes will be displayed here -->
                                        </div>
                                    </div>

                                    <div class="form-group text-center">
                                        <button id="savebarcode" type="button" class="btn btn-warning" style="margin-top: 15px;" disabled><i class="fa fa-download"></i> Download Zip</button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

		</div>
	</div>
	<div class="navbar-fixed-bottom" >
		<div class="col-menu">
			<div class="btn-group col-xs-12 col-sm-12 col-md-12" style="background-color:#8F8F8F; height:inherit;">
				<div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-7 col-md-offset-5 col-lg-4 col-lg-offset-8">
                    <button class="btn btn-warning col-xs-6 col-sm-6 col-md-6 col-lg-6" style=" visibility:hidden; border-radius:0px; line-height:29px;" type="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> New Asset</button>
                    <!-- <button id="savebtn" class="waiting btn btn-primary col-xs-6 col-sm-6 col-md-6 col-lg-6" style="border-radius:0px; line-height:29px;" role="button"><span class="spinnerbtn"><i class="fa fa-spinner fa-spin hide"></i></span> Save</button>-->
                </div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.0/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    
const currentYear = new Date().getFullYear();
document.getElementById('year').value = currentYear;

// Get a reference to the <select> element
const monthSelect = document.getElementById('month');

// Array of month names
const monthData = [
    { value: 'AL', name: 'January' },
    { value: 'BK', name: 'February' },
    { value: 'CJ', name: 'March' },
    { value: 'DI', name: 'April' },
    { value: 'EH', name: 'May' },
    { value: 'FG', name: 'June' },
    { value: 'GF', name: 'July' },
    { value: 'HE', name: 'August' },
    { value: 'ID', name: 'September' },
    { value: 'JC', name: 'October' },
    { value: 'KB', name: 'November' },
    { value: 'LA', name: 'December' }
];

// Get the current date and month
const currentDate = new Date();
const currentMonth = currentDate.getMonth();

// Populate the <select> with month options
monthData.forEach((month, index) => {
    const option = document.createElement('option');
    option.value = month.value;
    option.textContent = month.name;
    monthSelect.appendChild(option);

    // Automatically select the current month
    if (index === currentMonth) {
        option.selected = true;
    }
});

// Define an array to store the separate values
const separateValues = [];
const separateYear = [];
const separateBatch = [];
const separateMonth = [];
const separateSeries = [];
const maskedSeries = [];

const generatedCardNumbers = [];

const letterToDigit = {
    "A": "1",
    "B": "2",
    "C": "3",
    "D": "4",
    "E": "5",
    "F": "6",
    "G": "7",
    "H": "8",
    "I": "9",
    "J": "0"
};

$.ajax({
    url: "{{ route('cardnumber.getLastBatch') }}",
    type: 'GET',
    dataType: 'json',
    success: function(data) {
        const lastBatch = data.lastBatch; // Get the last series number from the response
        console.log("Last Batch: ", lastBatch);

        // Convert letters to digits
        const digitBatch = lastBatch.split('').map(letter => letterToDigit[letter]).join('');

        let incrementedBatch;
        if (lastBatch === "00") {
            incrementedBatch = "1";
        } else {

            incrementedBatch = (parseInt(digitBatch) + 1).toString();
        }
        document.getElementById('batch').value = incrementedBatch;
    }
});

function jumbleString(input) {
    var result = input.split('');

    swap(result, 0, 2);
    swap(result, 1, 3);
    swap(result, 4, 6);
    swap(result, 5, 8);
    swap(result, 7, 9);

    return result.join('');
}

function swap(arr, i, j) {
    var temp = arr[i];
    arr[i] = arr[j];
    arr[j] = temp;
}


document.getElementById("generate").addEventListener("click", function (event) {
    event.preventDefault();

    const yearInput = document.getElementById("year").value;
    const lastTwoDigits = yearInput.slice(-2);
    const digitToLetter = {
        "1": "A",
        "2": "B",
        "3": "C",
        "4": "D",
        "5": "E",
        "6": "F",
        "7": "G",
        "8": "H",
        "9": "I",
        "0": "J"
    };

    const year = lastTwoDigits.split("").map(digit => digitToLetter[digit]).join("");
    const batchInput = document.getElementById("batch").value;
    const formattedBatch = batchInput.padStart(2, '0'); // Ensure it's always two characters
    const batch = formattedBatch.split("").map(digit => digitToLetter[digit]).join("");
    const month = document.getElementById("month").value;
    
    const numToGenerate = parseInt(document.getElementById("series").value);
    const resultsContainer = document.getElementById("results");
    resultsContainer.innerHTML = ""; // Clear previous results
    const resultsContainer2 = document.getElementById("results2");
    resultsContainer2.innerHTML = ""; // Clear previous results

    if (isNaN(numToGenerate) || numToGenerate <= 0) {
        const warningMessage = 'Please fill in a valid number of series to generate.';
        const alertMessage = $('#alertMessage');
        alertMessage.html(warningMessage);
        alertMessage.removeClass('alert-success').addClass('alert-danger').show();

        setTimeout(function () {
            alertMessage.hide();
        }, 1000);
    }else{

    $.ajax({
        type: "GET",
        url: "{{ route('cardnumber.getLastSeriesNumber') }}",
        success: function (data) {
            const lastSeries = data.lastSeries; // Get the last series number from the response
                    // Use the jumbleString function to manipulate lastSeries
        const jumbledLastSeries = jumbleString(lastSeries);

        console.log("lastSeries: ", lastSeries);
        console.log("Jumbled lastSeries: ", jumbledLastSeries);
            // Continue generating new card numbers starting from the next series number
            const numToGenerate = parseInt(document.getElementById("series").value);
            const resultsContainer = document.getElementById("results");
            resultsContainer.innerHTML = ""; // Clear previous results

            const resultsContainer2 = document.getElementById("results2");
            resultsContainer2.innerHTML = ""; // Clear previous results

            for (let i = 1; i <= numToGenerate; i++) {
                const lastSeriesNumber = parseInt(lastSeries, 10); // Convert lastSeries to a number
                const incrementedNumber = lastSeriesNumber + i;
                const series = incrementedNumber.toString().padStart(10, '0');
                const jumbledSeries = jumbleString(series);

                const cardNumber = `${year}${batch}${month}${jumbledSeries}`;

                // Push the cardNumber into the separateValues array
                separateValues.push(cardNumber);
                separateYear.push(year);
                separateBatch.push(batch);
                separateMonth.push(month);
                separateSeries.push(series);
                maskedSeries.push(jumbledSeries);
                generatedCardNumbers.push(cardNumber);
                // Create a <p> element for the numbers and append it to resultsContainer
                const p = document.createElement("p");
                p.textContent = cardNumber;
                console.log("CARD:", p.textContent);
                resultsContainer.appendChild(p);

            // Generate the barcode image source using JsBarcode
            const barcodeImage = document.createElement('img');
            //cardNumber.match(/.{1,4}/g).join('-') cardnumber with dashj
            console.log("CARDNUMBER:", cardNumber);
            JsBarcode(barcodeImage, cardNumber, { 
                    format: 'CODE128', 
                    height: 40, 
                    background: 'transparent',
                    displayValue: true,
                    text: cardNumber.match(/.{1,4}/g).join('-')
                }); // Generate the barcode

            barcodeImage.classList.add('image-spacing'); // Add a class for styling
            resultsContainer2.appendChild(barcodeImage);
            // Create an <img> element for the barcode image
            //const barcodeImageSrc = `{{ route('generate.barcode', '') }}/${cardNumber}`;
            //const barcodeImage = document.createElement('img');

            //barcodeImage.src = barcodeImageSrc;
            //console.log("BARCODE:",barcodeImageSrc);
            //barcodeImage.alt = `${cardNumber}`;
            //barcodeImage.classList.add('image-spacing'); 
            //console.log("barcodeImage:",barcodeImage);
            //resultsContainer2.appendChild(barcodeImage);
            localStorage.setItem('generatedHistory', JSON.stringify(generatedCardNumbers));
            }
        }
    });
    document.getElementById("savebtn").removeAttribute("disabled");
    document.getElementById("generate").setAttribute("disabled", "disabled");
    }
});



// Now, you have an array containing the separate values
console.log("Separate Values:", separateValues);
console.log("Separate Year:", separateYear);
console.log("Separate Batch:", separateBatch);
console.log("Separate Month:", separateMonth);
console.log("Separate Series:", separateSeries);
console.log("Masked Series:", maskedSeries);

//dito na ako na stop

$("#savebtn").click(function () {
    const year = separateYear;
    const batch = separateBatch;
    const month = separateMonth;
    const series = separateSeries;
    const maskedseries = maskedSeries;

    // Check if any of the required fields are empty
    if (!year[0] || !batch[0] || !month[0] || !series[0]) {
        // Show a warning message using Bootstrap alert
        const warningMessage = 'Please fill in all required fields.';
        const alertMessage = $('#alertMessage');
        alertMessage.html(warningMessage);
        alertMessage.removeClass('alert-success').addClass('alert-danger').show();
        return; // Don't proceed with the AJAX request
    }

    const ajaxPromises = [];

    for (let i = 0; i < year.length; i++) {
        const cardNumber = year[i] + batch[i] + month[i] + maskedseries[i];

        const promise = $.ajax({
            type: "POST",
            url: "{{ route('cardnumber.store') }}",
            data: {
                year: year[i],
                batch: batch[i],
                month: month[i],
                seriesnum: series[i],
                maskedseriesnum: maskedseries[i],
                card_number: cardNumber,
                _token: "{{ csrf_token() }}",
            }
        });

        ajaxPromises.push(promise);
    }

    $.when.apply($, ajaxPromises).done(function () {
        // Show a success message using Bootstrap alert
        const successMessage = 'Card numbers have been saved to the database.';
        const alertMessage = $('#alertMessage');
        alertMessage.html(successMessage);
        alertMessage.removeClass('alert-danger').addClass('alert-success').show();
        
        setTimeout(function () {
            alertMessage.hide();
        }, 2000); 
    });
    document.getElementById("savebarcode").removeAttribute("disabled");
    document.getElementById("savebtn").setAttribute("disabled", "disabled");
});

$("#savebarcode").click(function () {

    const yearInput = document.getElementById("year").value;
    const batchInput = 'BATCH_' + document.getElementById("batch").value;
    const currentYear = new Date().getFullYear().toString();

    if (!batchInput) {
        alert('Please enter the batch before saving.');
        return;
    }

    const zip = new JSZip();
    //const barcodesFolder = zip.folder("BARCODES"); // Top-level folder
    const yearFolder = zip.folder(currentYear); // Year folder
    const batchFolder = yearFolder.folder(batchInput); // Batch folder

    const images = document.querySelectorAll(".image-spacing");

    if (images.length === 0) {
        alert('No images to save.');
        return;
    }

    images.forEach(function (image, index) {
        const imgDataUrl = image.src;

        // Convert the data URL to a blob
        fetch(imgDataUrl)
            .then(response => response.blob())
            .then(blob => {
                batchFolder.file(`${separateValues[index]}.png`, blob);

                // If it's the last image, generate and download the ZIP
                if (index === images.length - 1) {
                    zip.generateAsync({ type: "blob" }).then(function (content) {
                        saveAs(content, "BARCODES.zip");

                        // Show success message and reload the page
                        const alertMessage = document.getElementById("alertMessage");
                        alertMessage.textContent = 'Barcodes are saved.';
                        alertMessage.classList.add('alert-success');
                        alertMessage.style.display = 'block';

                        setTimeout(function () {
                            alertMessage.style.display = 'none';
                            location.reload();
                        }, 3000);
                    });
                }
            });
    });
});

$("#history").click(function (){
    console.log("PININDOT MO HISTORY!");
    const resultsContainer = document.getElementById("results");
    resultsContainer.innerHTML = ""; // Clear previous results
    const resultsContainer2 = document.getElementById("results2");
    resultsContainer2.innerHTML = ""; // Clear previous results

    // Retrieve the entire generated card numbers array from local storage
    const generatedHistoryJSON = localStorage.getItem('generatedHistory');

    if (generatedHistoryJSON) {
        // Parse the JSON string to get the array
        const generatedHistory = JSON.parse(generatedHistoryJSON);

        // Loop through the array and display the card numbers and generate barcode images
        generatedHistory.forEach(cardNumber => {
            // Display the card number
            const p = document.createElement("p");
            p.textContent = cardNumber;
            resultsContainer.appendChild(p);

            // Generate the barcode image using JsBarcode and display it
            const barcodeImage = document.createElement('img');
            JsBarcode(barcodeImage, cardNumber, {
                format: 'CODE128',
                height: 40,
                background: 'transparent',
                displayValue: true,
                text: cardNumber.match(/.{1,4}/g).join('-')
            });
            barcodeImage.classList.add('image-spacing');
            resultsContainer2.appendChild(barcodeImage);
        });
    }
});
</script>
	
@endsection
