<?php

namespace App\DataObjects;

class BulletinData
{
    public $year;
    public $class;
    public $termType;
    public $students = [];
    public $statistics = [];
    public $principalTeacher;
    public $subjects = []; // ✅ AJOUTÉ pour éviter l'erreur

    public function __construct($year, $class, $termType)
    {
        $this->year = $year;
        $this->class = $class;
        $this->termType = $termType;
    }

    public function addStudent($studentData)
    {
        $this->students[] = $studentData;
    }

    public function setStudents(array $students): void
    {
        $this->students = $students;
    }

    public function setStatistics($statistics)
    {
        $this->statistics = $statistics;
    }

    public function setPrincipalTeacher($teacher)
    {
        $this->principalTeacher = $teacher;
    }

    // ✅ NOUVELLE MÉTHODE pour les matières
    public function setSubjects($subjects): void
    {
        $this->subjects = $subjects;
    }

    public function toArray()
    {
        return [
            'year' => $this->year,
            'class' => $this->class,
            'termType' => $this->termType,
            'students' => $this->students,
            'statistics' => $this->statistics,
            'principalTeacher' => $this->principalTeacher,
            'subjects' => $this->subjects, // ✅ Ajouté pour que le PDF voie la liste
        ];
    }
}
