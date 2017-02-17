<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Patient;
use AppBundle\Entity\Doctor;
use AppBundle\Repository\PatientRepository;
use AppBundle\Repository\HospitalRepository;
use AppBundle\Repository\DoctorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class HospitalController extends Controller
{

    /**
     * @Route("/hospital/add/patient" name="add_patient")
     */
    public function AddPatientAction()
    {
    	$params = array();
        $content = $this->get("request")->getContent();
        
        if (!empty($content)) {
            $patient = $this->getPatient($content);
            $patientRepo = new PatientRepository();
            
            if ($patientRepo->save($patient)) {
                $doctorId = $patient->getDoctor()->getId();
                return $this->redirectToRoute('doctor_detail', ['id'=> $doctorId]);
            }
        }

        return new JsonResponse([
            'msg' => 'Problem processing your request.. try again.'
        ]);
    }


    /**
     * @Route("/hospital/doctor/{doctor_id}" name="doctor_detail")
    */
    public function DoctorDetailAction($doctorId)
    {
        if (!empty($doctorId)) {
            
            $doctorRepo = new DoctorRepository();
            $doctor = $doctorRepo->selectById($doctorId);
            
            $patientRepo = new PatientRepository();
            $patientList = $patientRepo->selectByDoctor($doctor);
            
            return new JsonResponse([
                'doctor' => $doctor,
                'patientList' => $patientList
                'msg' => 'Here are the patients for '.$doctor->getName()
            ]);
            
        }

        return new JsonResponse([
            'msg' => 'Successfully added patient. Problem getting doctor details.'
        ]);
    }

    /**
     *@return Patient
    */
    private function getPatient($content)
    {
        $params = json_decode($content, true); 
        
        $patient = new Patient();
        $patient->setName($params['name']);
        $patient->setDob($params['dob']);
        $patient->setGender($params['gender']);
        
        $hostpitalId = $params['hospital_id'];
        if (!empty($hostpitalId)) {
            $hospitalRepo = new HospitalRepository();
            $hospital = $hospitalRepo->selectById($hostpitalId);
            $patient->setHospital($hospital);
        }
        
        $doctorId = $params['doctor_id'];
        if (!empty($doctorId)) {
            $doctorRepo = new DoctorRepository();
            $doctor = $doctorRepo->selectById($doctorId);
            $patient->setDoctor($doctor);
        }

        return $patient;
    }



}