<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Queuing Display</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" />

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
    }
    .container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .queue-table {
        flex: 1;
        padding-right: 20px;
    }
    table {
        border-collapse: collapse;
        width: 90%;
        height: 90%;
        margin: 0 auto;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    th {
        padding: 50px;
        text-align: center;
        border: 1px solid #ddd;
        font-size: 35px;
        background-color: #10069f;
        color: white;
        width: 387px;
    }
    td {
        padding: 50px;
        text-align: center;
        border: 1px solid #ddd;
        font-size: 60px;
        /* font-weight: bold; */
        width: 150px; /* Set a fixed width */
        height: 150px; /* Set a fixed height */
        box-sizing: border-box; /* Ensure padding and border are included in the width and height */
    }

    .video-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-left: 20px;
        box-sizing: border-box;
        height: 100%;
    }

    .video-frame {
        /* border: 1px solid #ccc; */
        height: auto;
        max-width: 100%;
        margin-bottom: 20px; /* Example margin to create space between video and date/time */
    }

    .logo {
        display: block;
        margin: 20px auto;
        max-width: 100%;
    }

    @keyframes neonGlow {
        0%,
        100% {
            text-shadow: 0 0 5px #fff, 0 0 10px #ffff00, 0 0 15px #ffff00, 0 0 20px #ffff00, 0 0 25px #ffff00, 0 0 30px #ffff00, 0 0 35px #ffff00;
        }
        50% {
            text-shadow: 0 0 10px #fff, 0 0 20px #ffff00, 0 0 30px #ffff00, 0 0 40px #ffff00, 0 0 50px #ffff00, 0 0 60px #ffff00, 0 0 70px #ffff00;
        }
    }


    .glow {
    animation: neonGlow 1s infinite alternate;
    }

    .slideshow-container {
        flex: 1;
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mySlides img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }

    .date-time {
        flex: 1;
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #current-date-time {
        font-weight: bold;
        font-size: 3vw; /* responsive font size */
        padding: 10px;
        border-radius: 8px;
        display: inline-block;
        margin-top: 10px;
        max-width: 100%;
        word-wrap: break-word;
        text-align: center;
    }
    /* Fade animation for slideshow */
    @keyframes fade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .fade {
        animation: fade 1.5s; /* Adjust animation duration as needed */
    }


    
</style>
</head>
<body>
    <div class="container">
        <!-- Left side: Queue Table -->
        <div class="queue-table">
            <!-- <h2>Queue Table</h2> -->
            <table border="1" style="width: 100%;">
                <thead>
                    <tr>
                        <th><i class="fas fa-cash-register"></i></th>
                        <th>Queue</th>
                        <th>Station</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- @foreach($queue as $queues)
                        @if ($loop->index < 5) 
                        <tr>
                            <td>{{ $queues->Counter }}</td>
                            <td>{{ $queues->QRCode }}</td>
                            <td>{{ $queues->Station }}</td>
                        </tr>
                        @else
                            @break 
                        @endif
                    @endforeach -->
                </tbody>
            </table>
        </div>

        <!-- Right side: Video Frame and Date/Time -->
        <div class="video-container">
            <!-- FOR YOUTUBE VIDEOS -->
            <!-- <div class="video-frame"> -->
                <!-- Insert your video player or video embed code here -->
                <!-- <iframe width="100%" height="720" src="https://www.youtube.com/embed/k85mRPqvMbE" title="Crazy Frog - Axel F (Official Video)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>            
            </div> -->

            <!-- FOR SLIDE SHOW OF PHOTOS -->
            <div class="slideshow-container" id="slideshow-container">
                @foreach($images as $image)
                    <div class="mySlides fade">
                        <img src="{{ asset($image->PictureLink) }}" alt="Image {{ $image->FileName }}">
                    </div>
                @endforeach
            </div>
            <!-- FOR DOWNLOADED VIDEO -->

            <div class="date-time">
                <p><span id="current-date-time"></span></p>
                <!-- <img src="{{asset('images/logo.png')}}" alt="Logo" class="logo"> -->
            </div>
        </div>


    </div>
    <audio id="announcement-audio" src="{{ url('mp3/ANNOUNCEMENT.mp3') }}"></audio>

    <script src="../../../js/socket.io.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var myURL = window.location.hostname;
        const socket = io.connect(myURL+':3001');

        socket.on("connect", () =>{
            socket.emit('display');
            console.log("connected");

            document.body.click();
        })
        
        socket.on('newQueueDisplay', function(queueDisplay) {
            console.log('New data received:');
            console.log('Counter:', queueDisplay.counter);
            console.log('Department:', queueDisplay.department);
            console.log('Queue No:', queueDisplay.queueNo);

            // Convert counter to a number
            queueDisplay.counter = Number(queueDisplay.counter); // Explicitly convert to number

            var tableBody = document.querySelector('table tbody');
            
            // Check if there's an existing row with the same queue number
            var existingRowByQueueNo = tableBody.querySelector('tr[data-queue-no="' + queueDisplay.queueNo + '"]');
            
            // Remove existing row with the same queue number if it exists
            if (existingRowByQueueNo) {
                tableBody.removeChild(existingRowByQueueNo);
            }

            // Check if there's an existing row with the same counter and department
            var existingRow = tableBody.querySelector('tr[data-counter="' + queueDisplay.counter + '"][data-department="' + queueDisplay.department + '"]');

            if (existingRow) {
                // If the row with the same counter and queue number exists, make it glow
                existingRow.cells[0].classList.add('glow'); // If counterCell exists
                existingRow.cells[1].classList.add('glow'); // Queue No
                existingRow.cells[2].classList.add('glow'); // Department

                // Remove glow effect after 10 seconds
                setTimeout(function() {
                    existingRow.cells[0].classList.remove('glow');
                    existingRow.cells[1].classList.remove('glow');
                    existingRow.cells[2].classList.remove('glow');
                }, 10000);
                
                // Update existing row with the new queue number
                existingRow.cells[1].textContent = queueDisplay.queueNo; // Update Queue No
                existingRow.cells[2].textContent = queueDisplay.department; // Update Department
            } else {
                // Create new row
                var newRow = document.createElement('tr');
                newRow.setAttribute('data-counter', queueDisplay.counter); // Set data attribute for counter
                newRow.setAttribute('data-department', queueDisplay.department); // Set data attribute for department
                newRow.setAttribute('data-queue-no', queueDisplay.queueNo); // Set data attribute for queue number

                // Only create and append counterCell if the counter is not 0
                if (queueDisplay.counter !== 0) {
                    var counterCell = document.createElement('td');
                    counterCell.textContent = queueDisplay.counter;
                    newRow.appendChild(counterCell);
                } else {
                    // Create a placeholder cell for the counter (can be empty)
                    var counterCellPlaceholder = document.createElement('td');
                    counterCellPlaceholder.textContent = ''; // Keep it empty
                    newRow.appendChild(counterCellPlaceholder);
                }

                var queueNoCell = document.createElement('td');
                queueNoCell.textContent = queueDisplay.queueNo;

                var departmentCell = document.createElement('td');
                departmentCell.textContent = queueDisplay.department;

                newRow.appendChild(queueNoCell);
                newRow.appendChild(departmentCell);

                // Check if the table already has 5 rows
                if (tableBody.rows.length >= 5) {
                    // Remove the last row
                    tableBody.removeChild(tableBody.lastElementChild);
                }

                // Insert the new row at the top
                tableBody.insertBefore(newRow, tableBody.firstChild);

                // Add glow effect
                if (queueDisplay.counter !== 0) {
                    counterCell.classList.add('glow');
                }
                queueNoCell.classList.add('glow');
                departmentCell.classList.add('glow');

                // Remove glow effect after 10 seconds
                setTimeout(function() {
                    if (queueDisplay.counter !== 0) {
                        counterCell.classList.remove('glow');
                    }
                    queueNoCell.classList.remove('glow');
                    departmentCell.classList.remove('glow');
                }, 10000);
            }

            // Call the speakText function
            speakText(queueDisplay.queueNo, queueDisplay.department, queueDisplay.counter);
        });

        // New socket listener for exiting a row
        socket.on('newqueueDisplayExit', function(queueDisplayExit) {
            console.log('Removing data:');
            console.log('Counter:', queueDisplayExit.counter);
            console.log('Department:', queueDisplayExit.department);
            console.log('Queue No:', queueDisplayExit.queueNo);

            var tableBody = document.querySelector('table tbody');

            // Find the row matching the counter, department, and queue number
            var rowToRemove = tableBody.querySelector('tr[data-counter="' + queueDisplayExit.counter + '"][data-department="' + queueDisplayExit.department + '"][data-queue-no="' + queueDisplayExit.queueNo + '"]');

            // Remove the row if it exists
            if (rowToRemove) {
                tableBody.removeChild(rowToRemove);
            }
        });

        // var slideIndex = 0;
        // carousel();

        // function carousel() {
        //     var i;
        //     var slides = document.getElementsByClassName("mySlides");
        //     for (i = 0; i < slides.length; i++) {
        //         slides[i].style.display = "none";
        //     }
        //     slideIndex++;
        //     if (slideIndex > slides.length) { slideIndex = 1 }
        //     slides[slideIndex - 1].style.display = "block";
        //     setTimeout(carousel, 10000); // Change image every 4 seconds
        // }

        // var slideIndex = 0;
        // var carouselTimeout;

        // function carousel() {
        //     var i;
        //     var slides = document.getElementsByClassName("mySlides");

        //     for (i = 0; i < slides.length; i++) {
        //         slides[i].style.display = "none";
        //     }
        //     slideIndex++;
        //     if (slideIndex > slides.length) { slideIndex = 1 }
        //     if(slides.length > 0){
        //         slides[slideIndex - 1].style.display = "block";
        //     }

        //     carouselTimeout = setTimeout(carousel, 10000);
        // }

        // carousel();



        function updateTime() {
            const now = new Date();
            const optionsDate = { year: 'numeric', month: 'long', day: 'numeric' };
            const optionsTime = { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true };
            const optionsDay = { weekday: 'short' };
            const formattedDate = now.toLocaleDateString('en-US', optionsDate);
            const formattedTime = now.toLocaleTimeString('en-US', optionsTime);
            const formattedDay = now.toLocaleDateString('en-US', optionsDay);
            const formattedDateTime = `${formattedDate} ${formattedDay}, ${formattedTime}`;
            document.getElementById('current-date-time').textContent = formattedDateTime;
        }
        updateTime();
        setInterval(updateTime, 1000); // Update time every second

        //LOAD VOICES
        // window.speechSynthesis.onvoiceschanged = function() {
        //     const voices = window.speechSynthesis.getVoices();
        // };

        function speakText(queueNo, department, counter) {

            const audio = document.getElementById('announcement-audio');
            audio.play();

            audio.onended = function() {
                const prefix = 'CALLING PATIENT: ';
                const suffix = ' please proceed to ';
                let station = department;
                // const counterText = (station !== 'Reception' && station !== 'Releasing') 
                //             ? ' Station Number ' + counter 
                //             : ' Counter Number ' + counter;

                const departmentMappings = {
                    'CTSCAN': 'C T Scan',
                    'GEN UTZ': 'General Ultrasound',
                    'MAMMO': 'Mammography',
                    'OB UTZ': 'O B Ultrasound',
                    'VASCULAR UTZ': 'Vascular Ultrasound',
                    '2DECHO': '2 D ECHO'
                };

                if (departmentMappings[station.toUpperCase()]) {
                    station = departmentMappings[station.toUpperCase()];
                }

                let fullText;

                if (Number(counter) === 0) {
                    fullText = `${prefix}${queueNo}${suffix}${station} Area`;
                } else {
                    const counterText = (station !== 'Reception' && station !== 'Releasing') 
                                ? ' Station Number ' + counter 
                                : ' Counter Number ' + counter;
                    fullText = `${prefix}${queueNo}${suffix}${station}${counterText}`;
                }
                
                const utterance = new SpeechSynthesisUtterance(fullText);
                // // Select a female voice
                // const voices = window.speechSynthesis.getVoices();
                // const femaleVoice = voices.find(voice => voice.name.toLowerCase().includes('female') || voice.name.toLowerCase().includes('woman') || voice.name.toLowerCase().includes('girl')) || voices[0];
                // utterance.voice = femaleVoice;

                window.speechSynthesis.speak(utterance);
            };
        }


        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function() {
                console.log('clicked');
            });

            document.body.click();
        });

        var slideIndex = 0;
        var carouselTimeout;
        var baseUrl = "{{ asset('') }}";

        function carousel() {
            var slides = document.getElementsByClassName("mySlides");
            if (slides.length === 0) {
                return;
            }
            for (var i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slideIndex++;
            if (slideIndex > slides.length) { slideIndex = 1; }
            slides[slideIndex - 1].style.display = "block";

            carouselTimeout = setTimeout(carousel, 5000);
        }

        function fetchUpdateDisplays() {
            $.ajax({
                url: '{{ route("fetchdisplaydata") }}',
                method: 'GET',
                success: function(response) {
                    console.log("Fetched images:", response);
                    let container = $('#slideshow-container');
                    container.empty();

                    response.forEach(function(image) {
                        let slide = $(`
                            <div class="mySlides fade">
                                <img src="${baseUrl}${image.PictureLink}" alt="Image ${image.FileName}">
                            </div>
                        `);
                        container.append(slide);
                    });

                    slideIndex = 0;

                    if (carouselTimeout) {
                        clearTimeout(carouselTimeout);
                    }

                    carousel();
                },
                error: function(err) {
                    console.error("Failed to fetch images:", err);
                }
            });
        }

        carousel();

        setInterval(fetchUpdateDisplays, 3600000);
    </script>
</body>
</html>