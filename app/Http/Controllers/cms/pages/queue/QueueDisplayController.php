<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\cms\Kiosk;
use App\Models\cms\QueueDisplay;
use Carbon\Carbon;


class QueueDisplayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $display = QueueDisplay::all();
    
        return view('cms.displayQueue', compact('display'));
    }

    public function create()
    {

	    return view('cms.displayQueueCreate');
    }

    public function display()
    {
        $this->updateExpiredDisplays();

        $today = Carbon::today()->toDateString();
        $queue = Kiosk::where('Date', $today)
                    ->where('Station', '!=', 'exitQueue')
                    ->get();

        $images = QueueDisplay::where('Status', 1)
                    ->whereDate('StartDate', '<=', $today)
                    ->whereDate('EndDate', '>=', $today)
                    ->get();
    
        return view('cms/pages.queueDisplay', compact('queue', 'images'));
    }

    public function fetchQueueData()
    {
        $today = Carbon::today()->toDateString();
        $queue = Kiosk::where('Date', $today)
                      ->where('Station', '!=', 'exitQueue')
                      ->take(5)
                      ->get();

        return response()->json($queue);
    }

    public function fetchDisplayData()
    {
        $this->updateExpiredDisplays();

        $today = Carbon::today()->toDateString();

        $images = QueueDisplay::where('Status', 1)
            ->whereDate('StartDate', '<=', $today)
            ->whereDate('EndDate', '>=', $today)
            ->get();

        return response()->json($images);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
      
	/**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Initialize $photoPath
        $photoPath = null;
    
        // Store DISPLAY PHOTO
        if ($request->file('imageUpload')) {
            $path = '/uploads/QueueDisplay/Images/';
            $file = $request->file('imageUpload');
    
            // Generate a unique filename
            $date = date('Y-m-d');
            $extension = $file->getClientOriginalExtension();
            $filename = $request->input('filename') . '.' . $extension;
    
            // Define destination path
            $destinationPath = public_path($path . $filename);
    
            // Resize the image to 1000x720 with white background and correct orientation
            $this->resizeImage($file->getPathname(), $destinationPath, 1000, 720);
    
            // Set the $photoPath
            $photoPath = $path . $filename;
        }
    
        $queueDisplay = new QueueDisplay();
    
        $queueDisplay->FileName = $request->input('filename');
        $queueDisplay->PictureLink = $photoPath;
        $queueDisplay->Status = $request->input('status');
        $queueDisplay->StartDate = date("Y-m-d", strtotime($request->input('startdate')));
        $queueDisplay->EndDate = date("Y-m-d", strtotime($request->input('enddate')));
        $queueDisplay->Notes = $request->input('notes');
        $queueDisplay->UploadDate = Carbon::now();
        $queueDisplay->UploadBy = Auth::user()->username;
    
        $queueDisplay->save();
    
        return redirect()->route('displayqueue.index')->with('success', 'Photo uploaded and resized successfully.');
    }
    

    private function resizeImage($sourcePath, $destinationPath, $targetWidth, $targetHeight)
    {
        // Correct the orientation before resizing
        $sourceImage = $this->correctOrientation($sourcePath);

        // Get the original dimensions
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Calculate aspect ratio
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        // Determine new dimensions
        if ($sourceAspect > $targetAspect) {
            // Source is wider
            $newWidth = $targetWidth;
            $newHeight = (int) ($targetWidth / $sourceAspect);
        } else {
            // Source is taller or equal
            $newWidth = (int) ($targetHeight * $sourceAspect);
            $newHeight = $targetHeight;
        }

        // Create a blank canvas with a white background
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $white = imagecolorallocate($canvas, 255, 255, 255); // White background
        imagefill($canvas, 0, 0, $white);

        // Resize and copy the image onto the canvas
        $xOffset = (int) (($targetWidth - $newWidth) / 2);
        $yOffset = (int) (($targetHeight - $newHeight) / 2);
        imagecopyresampled(
            $canvas,
            $sourceImage,
            $xOffset,
            $yOffset,
            0,
            0,
            $newWidth,
            $newHeight,
            $sourceWidth,
            $sourceHeight
        );

        // Save the processed image
        imagejpeg($canvas, $destinationPath, 90); // Save as JPEG with quality 90

        // Free resources
        imagedestroy($sourceImage);
        imagedestroy($canvas);
    }

    private function correctOrientation($sourcePath)
    {
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            if (!empty($exif['Orientation'])) {
                $sourceImage = imagecreatefromstring(file_get_contents($sourcePath));
                switch ($exif['Orientation']) {
                    case 3: // 180 degrees
                        $sourceImage = imagerotate($sourceImage, 180, 0);
                        break;
                    case 6: // 90 degrees clockwise
                        $sourceImage = imagerotate($sourceImage, -90, 0);
                        break;
                    case 8: // 90 degrees counterclockwise
                        $sourceImage = imagerotate($sourceImage, 90, 0);
                        break;
                }
                return $sourceImage; // Return corrected image resource
            }
        }

        // If no EXIF data or no orientation adjustment needed
        return imagecreatefromstring(file_get_contents($sourcePath));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $display = QueueDisplay::findOrFail($id);

        return view('cms.displayQueueEdit', ['display' => $display]);
    }
    
   /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        // Find the record by ID
        $queueDisplay = QueueDisplay::findOrFail($id);
    
        // Update the fields
        $queueDisplay->FileName = $request->input('filename');
        $queueDisplay->Status = $request->input('status');
        $queueDisplay->StartDate = date("Y-m-d", strtotime($request->input('startdate')));
        $queueDisplay->EndDate = date("Y-m-d", strtotime($request->input('enddate')));
        $queueDisplay->Notes = $request->input('notes');
        $queueDisplay->UpdateDate = Carbon::now();
        $queueDisplay->UpdateBy = Auth::user()->username;
    
        // Save the updated record
        $queueDisplay->save();
    
        return redirect()->route('displayqueue.edit', ['displayqueue' => $id])->with('success', 'Queue display updated successfully.');
    }

    public function updateExpiredDisplays()
    {
        $today = Carbon::today();

        QueueDisplay::where('Status', 1) 
            ->whereDate('EndDate', '<', $today)
            ->update(['Status' => 0]);

        return response()->json(['message' => 'Expired displays updated successfully.']);
    }
       
}
