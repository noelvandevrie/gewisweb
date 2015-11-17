<?php

namespace Company\Service;

//use Application\Service\AbstractService;
use Application\Service\AbstractAclService;

/**
 * Company service.
 */
class Company extends AbstractACLService
{
    /**
     * Returns an list of all companies (excluding hidden companies
     *
     */
    public function getCompanyList()
    {
        $translator = $this->getTranslator();
        if ($this->isAllowed('list')) {
            return $this->getCompanyMapper()->findAll($translator->getLocale());
        } else {
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed list the companies')
            );
        }
    }
    // Company list for admin interface
    /**
     * Returns a list of all companies (including hidden companies)
     *
     */
    public function getHiddenCompanyList()
    {
        if ($this->isAllowed('listall')) {
            return $this->getCompanyMapper()->findAll();
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to acces the admin interface')
            );
        }
    }

    /**
     * Checks if the data is valid, and if it is saves the package
     *
     * @param mixed $package
     * @param mixed $data
     */
    public function savePackageWithData($package,$data)
    {
        $packageForm = $this->getPackageForm();
        $packageForm->setData($data);
        if ($packageForm->isValid()){
            $package->exchangeArray($data); 
            $this->savePackage();
        }
    }

    /**
     * Checks if the data is valid, and if it is, saves the Company
     *
     * @param mixed $company
     * @param mixed $data
     */
    public function saveCompanyWithData($company,$data)
    {
        $companyForm = $this->getCompanyForm();
        $companyForm->setData($data);
        if ($companyForm->isValid()){
            $company->exchangeArray($data); 
            $this->saveCompany();
        }
    }

    /**
     * Checks if the data is valid, and if it is, saves the Job
     *
     * @param mixed $job
     * @param mixed $data
     */
    public function saveJobWithData($job,$data)
    {
        $jobForm = $this->getJobForm();
        $jobForm->setData($data);
        if ($jobForm->isValid()){
            $job->exchangeArray($data); 
            $this->saveJob();
        }
    }

    /**
     * Saves all modified jobs
     *
     */
    public function saveJob()
    {
        $this->getJobMapper()->save();
    }

    /**
     * Saves all modified companies
     *
     */
    public function saveCompany()
    {
        $this->getCompanyMapper()->save();
    }

    /**
     * Saves all modified packages
     *
     */
    public function savePackage()
    {
        $this->getPackageMapper()->save();
    }

    /**
     * Checks if the data is valid, and if it is, inserts the company, and sets 
     * all data
     *
     * @param mixed $data
     */
    public function insertCompanyWithData($data)
    {
        $companyForm = $this->getCompanyForm();
        $companyForm->setData($data);
        if ($companyForm->isValid()) {
            $company = $this->insertCompany($data['languages']);
            $company->exchangeArray($data);
            $this->saveCompany();
            return true;
        }
        return false;
    }
    /**
     * Inserts the company and initializes translations for the given languages
     *
     * @param mixed $languages
     */
    public function insertCompany($languages)
    {
        if ($this->isAllowed('insert')) {
            return $this->getCompanyMapper()->insert($languages);
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to insert a company')
            );
        }
    }

    /**
     * Checks if the data is valid, and if it is, inserts the package, and assigns it to the given company
     *
     * @param mixed $companySlugName
     * @param mixed $data
     */
    public function insertPackageForCompanySlugNameWithData($companySlugName,$data)
    {
        $packageForm = $this->getPackageForm();
        $packageForm->setData($data);
        if ($packageForm->isValid()) {
            $package = $this->insertPackageForCompanySlugName($companySlugName);
            $package->exchangeArray($data);
            $this->savePackage();
            return true;
        }
        return false;
    }

    /**
     * Inserts a package and assigns it to the given company
     *
     * @param mixed $companySlugName
     */
    public function insertPackageForCompanySlugName($companySlugName)
    {
        if ($this->isAllowed('insert')) {
            $companies = $this->getEditableCompaniesWithSlugName($companySlugName);
            var_dump($companySlugName);
            $company = $companies[0];

            return $this->getPackageMapper()->insertPackageIntoCompany($company);
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to insert a package')
            );
        }
    }
    /**
     * Checks if the data is valid, and if it is, assigns a job, and bind it to 
     * the given package
     *
     * @param mixed $packageID
     * @param mixed $data
     */
    public function insertJobIntoPackageIDWithData($packageID,$data)
    {
        $jobForm = $this->getJobForm();
        $jobForm->setData($data);
        if ($jobForm->isValid()) {
            $job = $this->insertJobIntoPackageID($packageId);
            $job->exchangeArray($data);
            $this->saveCompany();
            return true;
        }
        return false;
    }
    /**
     * Inserts a job, and binds it to the given package
     *
     * @param mixed $packageID
     */
    public function insertJobIntoPackageID($packageID)
    {
        if ($this->isAllowed('insert')) {
            $package = $this->getEditablePackage($packageID);
            $result = $this->getJobMapper()->insertIntoPackage($package);

            return $result;
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to insert a job')
            );
        }
    }

    /**
     * Deletes the given package
     *
     * @param mixed $packageID
     */
    public function deletePackage($packageID)
    {
        if ($this->isAllowed('delete')) {
            return $this->getPackageMapper()->delete($packageID);
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to delete packages')
            );
        }
    }
    /**
     * Deletes the company identified with $slug
     *
     * @param mixed $slug
     */
    public function deleteCompaniesWithSlug($slug)
    {
        if ($this->isAllowed('delete')) {
            return $this->getCompanyMapper()->deleteWithSlug($slug);
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to delete companies')
            );
        }
    }

    /**
     * Returns all jobs that are owned by a company identified with 
     * $companySlugname
     *
     * @param mixed $companySlugName
     */
    public function getJobsWithCompanySlugName($companySlugName)
    {
        $return = $this->getJobMapper()->findJobsWithCompanySlugName($companySlugName);

        return $return;
    }

    /**
     * Returns all companies identified with $slugName
     *
     * @param mixed $slugName
     */
    public function getCompaniesWithSlugName($slugName)
    {
        return $this->getCompanyMapper()->findCompaniesWithSlugName($slugName);
    }

    /**
     * Returns a persistent package
     *
     * @param mixed $packageID
     */
    public function getEditablePackage($packageID)
    {
        if ($this->isAllowed('edit')) {
            $package = $this->getPackageMapper()->findEditablePackage($packageID);

            return $package;
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to edit packages')
            );
        }
    }

    /**
     * Returns all companies with a given $slugName and makes them persistent
     *
     * @param mixed $slugName
     */
    public function getEditableCompaniesWithSlugName($slugName)
    {
        if ($this->isAllowed('edit')) {
            return $this->getCompanyMapper()->findEditableCompaniesWithSlugName($slugName, true);
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to edit companies')
            );
        }
    }

    /**
     * Returns all jobs with a given slugname, owned by a company with 
     * $companySlugName
     *
     * @param mixed $companySlugName
     * @param mixed $jobSlugName
     */
    public function getEditableJobsWithSlugName($companySlugName, $jobSlugName)
    {
        if ($this->isAllowed('edit')) {
            return $this->getJobMapper()->findJobWithSlugName($companySlugName, $jobSlugName);
        } else {
            $translator = $this->getTranslator();
            throw new \User\Permissions\NotAllowedException(
                $translator->translate('You are not allowed to edit jobs')
            );
        }
    }

    /**
     * Returns all jobs with a $jobSlugName, owned by a company with a 
     * $companySlugName
     *
     * @param mixed $companySlugName
     * @param mixed $jobSlugName
     */
    public function getJobsWithSlugName($companySlugName, $jobSlugName)
    {
        return $this->getJobMapper()->findJobWithSlugName($companySlugName, $jobSlugName);
    }

    /**
     * Returns all jobs in the database
     *
     */
    public function getJobList()
    {
        return $this->getJobMapper()->findAll();
    }

    /**
     * Get the Company Edit form.
     *
     * @return Company Edit form
     */
    public function getCompanyForm()
    {
        return $this->sm->get('company_admin_edit_company_form');
    }

    /**
     * Returns a the form for entering packages
     *
     */
    public function getPackageForm()
    {
        return $this->sm->get('company_admin_edit_package_form');
    }

    /**
     * Returns the form for entering jobs
     *
     */
    public function getJobForm()
    {
        return $this->sm->get('company_admin_edit_job_form');
    }

    /**
     * Returns all jobs that are active
     *
     */
    public function getActiveJobList()
    {
        $jl = $this->getJobList();
        $r = array();
        foreach ($jl as $j) {
            if ($j->getActive()) {
                array_push($r, $j);
            }
        }

        return $r;
    }

    /**
     * Returns the companyMapper
     *
     */
    public function getCompanyMapper()
    {
        return $this->sm->get('company_mapper_company');
    }

    /**
     * Returns the packageMapper
     *
     */
    public function getPackageMapper()
    {
        return $this->sm->get('company_mapper_package');
    }

    /**
     * Returns the jobMapper
     *
     */
    public function getJobMapper()
    {
        return $this->sm->get('company_mapper_job');
    }
    /**
     * Get the Acl.
     *
     * @return Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        return $this->getServiceManager()->get('company_acl');
    }

    /**
     * Get the default resource ID.
     *
     * @return string
     */
    protected function getDefaultResourceId()
    {
        return 'company';
    }
}
