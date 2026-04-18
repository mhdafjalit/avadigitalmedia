<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Kiwilan\Audio\Audio;

#[AllowDynamicProperties]
class AudioLib {

    public function __construct() {
        $this->ci = &get_instance();
    }

    // Get audio duration (in seconds)
    public function get_media_duration($file_path) {
       $audio = Audio::read($file_path);
       
       return $audio->getDurationHuman();
    }

    // Get audio format (e.g., mp3, wav)
    public function get_media_format($file_path) {
         $audio = Audio::read($file_path);
        return $audio->getFormat();
    }

    public function get_video_duration($file_path) {
        // Make sure the file exists before proceeding
        if (!file_exists($file_path)) {
            return false;
        }
        $cmd = "ffprobe -v quiet -print_format json -show_format -show_streams " . escapeshellarg($file_path);
        $output = shell_exec($cmd);
        $data = json_decode($output, true);
        if (isset($data['format']['duration'])) {
            return (float) $data['format']['duration'];
        }

        return false;
    }
}
