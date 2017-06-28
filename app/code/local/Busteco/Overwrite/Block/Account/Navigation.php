<?php

class Busteco_Overwrite_Block_Account_Navigation extends Mage_Core_Block_Template
{
    protected $_links = array();

    protected $_activeLink = false;

    public function addLink($name, $path, $label, $urlParams = array(), $position = null)
    {
        if (is_null($label) || false === $label) {
            return $this;
        }
        
        $link = new Varien_Object(array(
            'name'          => $name,
            'path'          => $path,
            'label'         => $label,
            'url'           => $this->getUrl($path, $urlParams),
            'position'      => $position,
        ));

        if (intval($position) > 0) {
            while (isset($this->_links[$position])) {
                $position++;
            }
            $this->_links[$position] = $link;
            ksort($this->_links);
        } else {
            $position = 0;
            foreach ($this->_links as $k=>$v) {
                $position = $k;
            }
            $this->_links[$position+10] = $link;
        }
        
        return $this;
    }

    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getAction()->getFullActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        }
        return false;
    }
    
    protected function _prepareParams($params)
    {
        if (is_string($params)) {
            return $params;
        } elseif (is_array($params)) {
            $result = '';
            foreach ($params as $key=>$value) {
                $result .= ' ' . $key . '="' . addslashes($value) . '"';
            }
            return $result;
        }
        return '';
    }

    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
                // no break

            case 2:
                $path .= '/index';
        }
        return $path;
    }
    /**
     * Removes link by name
     *
     * @param string $name
     * @return Mage_Page_Block_Template_Links
     */
    public function removeLinkByName($name)
    {
        foreach ($this->_links as $k => $v) {
            if ($v->getName() == $name) {
                unset($this->_links[$k]);
            }
        }

        return $this;
    }

} 