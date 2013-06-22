<?php

namespace Redmine\Api;

/**
 * Listing roles
 *
 * @link   http://www.redmine.org/projects/redmine/wiki/Rest_Roles
 * @author Kevin Saliou <kevin at saliou dot name>
 */
class Role extends AbstractApi
{
    private $roles = array();

    /**
     * List roles
     * @link http://www.redmine.org/projects/redmine/wiki/Rest_Roles#GET
     *
     * @return array list of roles found
     */
    public function all()
    {
        $this->roles = $this->get('/roles.json');

        return $this->roles;
    }

    /**
     * Returns an array of roles with name/id pairs
     *
     * @param  boolean $forceUpdate to force the update of the roles var
     * @return array   list of roles (id => name)
     */
    public function listing($forceUpdate = false)
    {
        if (empty($this->roles)) {
            $this->all();
        }
        $ret = array();
        foreach ($this->roles['roles'] as $e) {
            $ret[$e['name']] = (int) $e['id'];
        }

        return $ret;
    }

    /**
     * Returns the list of permissions for a given role (Redmine v2.2.0)
     * @link http://www.redmine.org/projects/redmine/wiki/Rest_Roles#GET-2
     *
     * @param  int   $id the role id
     * @return array
     */
    public function show($id)
    {
        return $this->get('/roles/'.urlencode($id).'.json');
    }
}
