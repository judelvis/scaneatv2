<?php

class Images extends Result{
    
    var $images;
    
    function setImages($s) {
		$this->images = $s;
	}
	
	function getImages() {
		return $this->images;
	}

	static function uploadImages($directory, $main_id, $secondary_id, $uploadedFiles) {
        $uploaded = 1;
        $count = 1;
        $images = array();
        foreach ($uploadedFiles as $uploadedFile) {
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $basename = $main_id."_".$secondary_id."_".$count;
                $filename = Images::moveUploadedFile($directory,$basename, $uploadedFile);
                $uploaded += 1;
                array_push($images,$count.":Uploaded");
                if ($uploaded == 5) {
                    break;
                }
            } else {
                array_push($images,$count.":Error");
            }
            $count += 1;
        }
        $result = new Images();
        $result->setStatus(OK);
        $result->setMessage("");
        $result->setImages($images);
        return $result;
    }

    static function moveUploadedFile($directory, $basename, $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}