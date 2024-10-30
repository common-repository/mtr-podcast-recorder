<?php
/**
 * Playlist is a wrapper class around the SimpleXMLElement class to ease the
 * work with performing tasks on a playlist.
 */
class Playlist
{
    private $xml;
    private $file;

    /**
     * Constructor
     *
     * @param string $file Name of the XML file to work with.
     */
    public function __construct($file = false)
    {
        $this->file = $file;

        if ($file)
            $this->xml = @simplexml_load_file($file);
    }

    /**
     * Generates the XML string for an empty playlist, but does not create any
     * physical files.
     *
     * @param string $file
     * @return string XML string
     */
    function generateEmptyPlaylistXml($file)
    {
        $nameOnly = substr(basename($file), 0, strrpos($file, "."));
        $xmlString = "<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">\n".
                     "    <title>".$nameOnly."</title>\n".
                     "    <creator>MTR Podcast Recorder</creator>\n".
                     "    <info>http://mtr-design.com</info>\n".
                     "    <trackList>\n".
                     "        <!-- placeholder comment -->".
                     "    </trackList>\n".
                     "</playlist>";

        return $xmlString;
    }

    /**
     * Create an XML playlist file.
     *
     * @param string $file
     * @return True or False
     */
    public function createPlaylist($file)
    {
        $xmlString = $this->generateEmptyPlaylistXml($file);
        $xmlObj = new SimpleXMLElement($xmlString);
        $result = $xmlObj->asXML($file);

        $this->file = $file;

        return $result; // T or F
    }

    /**
     * Detele a node from the XML file by rebuilding the XML tree.
     *
     * @param string $filename Full path to the recording to be deleted.
     * @return string "OK" if no errors, otherwise, an error message.
     */
    public function deleteNode($filename)
    {
        if ($this->xml == false)
            return false;

        $xmlString = $this->generateEmptyPlaylistXml($this->file);
        $newXml = new SimpleXMLElement($xmlString);

        foreach ($this->xml->trackList->children() as $track)
        {
            $absLoc = trim($track->title);

            $skip = 0;

            // Find any invalid entries or the one specified to be deleted
            if ( $absLoc == basename($filename) || $absLoc == "" || !file_exists( dirname($filename)."/".$absLoc ) )
                $skip = 1;

            if ($skip) continue;

            // Create new node to the $newXml object
            $node = $newXml->trackList->addChild("track", "");
            foreach ($track->children() as $tag => $value)
                $node->addChild($tag, $value);
        }

        $this->xml = $newXml;
        $this->savePrettyXml($this->file);
    }

    /**
     * Saves the generated XML in with a nicely formatted structure
     * @param string $file Full name of the XML file to be saved.
     * @return int Number of bytes written.
     */
    public function savePrettyXml($xmlfile)
    {
        // The order of these statements is crucial for a human-readable XML
        $doc = new DOMDocument("1.0");
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($this->xml->asXML());
        $doc->formatOutput = true;
        return $doc->save($xmlfile);
    }

    /**
     * Move a recording one level up or down in a playlist by rebuilding the
     * XML tree.
     *
     * @param string $recFilename
     * @param string $direction
     */
    public function moveNode($recFilename, $direction)
    {
        if ($this->xml == false)
            return false;

        if ($direction != "up" && $direction != "down")
            return false;

        $xmlString = $this->generateEmptyPlaylistXml($this->file);
        $newXml = new SimpleXMLElement($xmlString);

        $p = -1;
        $q = -1;

        // Find the index of the element to move
        for ($i = 0; $i < sizeof($this->xml->trackList->track); $i++ )
            if ($this->xml->trackList->track[$i]->title == basename($recFilename))
                break;

        if ($direction == "up")
        {
            $p = $i - 1;
            $q = $i;

            if ($q == 0) return;
        }
        else if ($direction == "down")
        {
            $p = $i;
            $q = $i + 1;

            if ($p >= sizeof($this->xml->trackList->track)-1) return;
        }

        // Readd all elements up to $p
        for ($i = 0; $i < $p; $i++ )
        {
            $node = $newXml->trackList->addChild($this->xml->trackList->track[$i]->getName());
            foreach ($this->xml->trackList->track[$i]->children() as $tag => $value)
                $node->addChild($tag, $value);
        }

        // Swap the nodes with $p and $q indexes; $q first, then $p
        $node = $newXml->trackList->addChild($this->xml->trackList->track[$q]->getName());
        foreach ($this->xml->trackList->track[$q]->children() as $tag => $value)
            $node->addChild($tag, $value);

        $node = $newXml->trackList->addChild($this->xml->trackList->track[$p]->getName());
        foreach ($this->xml->trackList->track[$p]->children() as $tag => $value)
            $node->addChild($tag, $value);

        // Readd all elements after $q
        for ($i = $q + 1; $i < sizeof($this->xml->trackList->track); $i++ )
        {
            $node = $newXml->trackList->addChild($this->xml->trackList->track[$i]->getName());
            foreach ($this->xml->trackList->track[$i]->children() as $tag => $value)
                $node->addChild($tag, $value);
        }

        $this->xml = $newXml;
        $this->savePrettyXml($this->file);

        return true;
    }

    /**
     * Sets the status of a recording: on or off
     *
     * @param string $song
     * @param string $status
     */
    function setStatus($song, $status)
    {
        for ($i = 0; $i < sizeof($this->xml->trackList->track); $i++ ) {
            if ($this->xml->trackList->track[$i]->title == basename($song)) {

                if (!isset($this->xml->trackList->track[$i]->annotation) || trim($this->xml->trackList->track[$i]->annotation) == "")
                    $this->xml->trackList->track[$i]->addChild("annotation", "on");

                $this->xml->trackList->track[$i]->annotation = $status;
                break;
            }
        }

        $this->savePrettyXml($this->file);
    }

    /**
     * Return the node attributes wrapped in an XMLElement object array
     *
     * @param string $fullFilename
     * @param string $playlist
     * @return array
     */
    function getRecordingData($fullFilename, $playlist = "")
    {
        if ($playlist == "")
            $playlist = $this->file;

        $attr = array();
        for ($i = 0; $i < sizeof($this->xml->trackList->track); $i++)
        {
            if ($this->xml->trackList->track[$i]->title == basename($fullFilename))
            {
                foreach ($this->xml->trackList->track[$i]->children() as $tag => $value)
                    $attr[$tag] = (string)$value;
                break;
            }
        }
        return $attr;
    }

    /**
     * Add a track to a playlist with the given meta data
     *
     * @param string $playlist
     * @param string $filename
     * @param string $artist
     * @param string $album
     * @param string $title
     * @param string $info
     * @param string $image
     * @param string $status
     * @return True or False
     */
    function addToPlaylist($playlist = "", $filename, $httpLoc = "", $artist = "", $album = "", $image = "", $info = "http://mtr-design.com", $status = "on")
    {
        if ($playlist == "")
            $playlist = $this->file;

        if ($httpLoc == "")
            $httpLoc = $filename;

        // Create a SimpleXML instance from the existing XML file
        $xml = @simplexml_load_file($playlist);
        if (!$xml)
            return "ERROR: Cannot update playlist - it appears to be corrupted!";

        // If this file is already in the playlist, don't update the playlist
        if ($this->isSongInPlaylist($playlist, $filename))
            return false;

        // Add an element to the root of the XML
        $song = $xml->trackList->addChild("track", "");

        // Add attributes to the <track> element
        $song->addChild("location", $httpLoc);
        $song->addChild("creator", $artist);
        $song->addChild("album", $album);
        $song->addChild("title", $filename);
        $song->addChild("info", $info);
        $song->addChild("image", $image);
        $song->addChild("annotation", $status);

        $this->xml = $xml;

        // Save the XML back to a file
        $this->savePrettyXml($playlist);

        return true;
    }

    /**
     * Checks to see if a song is in a playlist.
     *
     * @param string $playlist
     * @param string $filename
     * @return True or False
     */
    function isSongInPlaylist($playlist = "", $filename)
    {
        if ($playlist == "")
            $playlist = $this->file;

        $xml = simplexml_load_file($playlist);

        foreach ($xml->trackList->children() as $child)
        {
            $isSameFilename = false;

            foreach ($child->children() as $name => $value)
            {
               if ($name == "title" && $filename == $value)
                   $isSameFilename = true;
            }

            // If all are true, then the song is already in the playlist
            if ($isSameFilename)
                return true;
        }

        return false;
    }

    /**
     * Verifies if a file is an XSPF playlist
     *
     * @param string $xmlFile
     * @return True or False
     */
    function isPlaylist($xmlFile = "")
    {
        if ($xmlFile == "")
            $xmlFile = $this->file;

        // Try to load the XML file
        $xml = simplexml_load_file($xmlFile);
        if ($xml === false)
            return false;

        // Root element should be named "playlist" in XML playlists
        if ($xml->getName() != "playlist")
            return false;

        return true;
    }
}
?>