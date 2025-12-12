<?php

namespace App\Services;

use App\Models\AssignStudent;
use App\Models\DiscountStudent;
use App\Models\StudentYear;
use App\Models\User;
use App\Models\StudentAbsence;
use App\Models\StudentMarks;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentRegistrationService
{
    /* public function create(array $data): void
    {
        DB::transaction(function () use ($data) {

            // 1) Créer l'utilisateur (Élève)
            $user = new User();
            $code = rand(0, 9999); // code 4 chiffres
            $user->usertype    = 'Élève';
            $user->code        = $code;
            $user->password    = bcrypt($code);

            $user->name        = $data['name'];
            $user->fname       = $data['fname'];
            $user->mname       = $data['mname'];
            $user->mobile      = $data['mobile'];
            $user->address     = $data['address'];
            $user->gender      = $data['gender'];
            $user->religion    = $data['religion'];
            $user->dob         = date('Y-m-d', strtotime($data['dob']));
            $user->lob         = $data['lob'];
            $user->f_no        = $data['f_no'];
            $user->statusclass = $data['statusclass'];

            if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                $file     = $data['image'];
                $filename = date('YmdHi').$file->getClientOriginalName();
                $file->move(public_path('upload/student_images'), $filename);
                $user->image = $filename;
            }
            $user->save();

            // 2) Générer id_no après avoir l'ID (anti-concurrence)
            $yearName = StudentYear::findOrFail($data['year_id'])->name; // ex: "2024-2025"
            $idNo     = str_pad((string)$user->id, 4, '0', STR_PAD_LEFT);
            $user->id_no = "{$yearName}{$idNo}";
            $user->save();

            // 3) Affectation à une classe/année
            $assign = new AssignStudent();
            $assign->student_id = $user->id;
            $assign->year_id    = $data['year_id'];
            $assign->class_id   = $data['class_id'];
            $assign->group_id   = $data['group_id'];
            $assign->shift_id   = $data['shift_id'];
            $assign->save();

            // 4) Réduction (facultative)
            if (isset($data['discount'])) {
                $discount = new DiscountStudent();
                $discount->assign_student_id = $assign->id;
                $discount->fee_category_id   = 1; // si fixe
                $discount->discount          = $data['discount'] ?? 0;
                $discount->save();
            }
        });
    } */

    public function create(array $data): void
{
    DB::transaction(function () use ($data) {
        // 1) Créer l'utilisateur (Élève)
        $user = new User();
        $code = rand(1000, 9999); // code 4 chiffres (de 1000 à 9999)
        $user->usertype    = 'Élève';
        $user->code        = $code;
        $user->password    = bcrypt($code);

        // Assignation des données avec des valeurs par défaut pour les champs nullable
        $user->name        = $data['name'];
        $user->fname       = $data['fname'] ?? null;
        $user->mname       = $data['mname'] ?? null;
        $user->mobile      = $data['mobile'] ?? null;
        $user->address     = $data['address'] ?? null;
        $user->gender      = $data['gender'];
        $user->religion    = $data['religion'] ?? null;
        $user->dob         = !empty($data['dob']) ? date('Y-m-d', strtotime($data['dob'])) : null;
        $user->lob         = $data['lob'] ?? null;
        $user->f_no        = $data['f_no'] ?? null;
        $user->statusclass = $data['statusclass'];

        // Gestion de l'image
        if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $file = $data['image'];
            $filename = date('YmdHi').'_'.$file->getClientOriginalName();
            $file->move(public_path('upload/student_images'), $filename);
            $user->image = $filename;
        }
        
        $user->save();

        // 2) Générer id_no après avoir l'ID
        $yearName = StudentYear::findOrFail($data['year_id'])->name;
        $idNo = str_pad((string)$user->id, 4, '0', STR_PAD_LEFT);
        $user->id_no = "{$yearName}{$idNo}";
        $user->save();

        // 3) Affectation à une classe/année
        $assign = new AssignStudent();
        $assign->student_id = $user->id;
        $assign->year_id    = $data['year_id'];
        $assign->class_id   = $data['class_id'];
        $assign->group_id   = $data['group_id'];
        $assign->shift_id   = $data['shift_id'] ?? null; // nullable
        $assign->save();

        // 4) Réduction (facultative)
        if (!empty($data['discount'])) {
            $discount = new DiscountStudent();
            $discount->assign_student_id = $assign->id;
            $discount->fee_category_id   = 1;
            $discount->discount          = $data['discount'];
            $discount->save();
        }
    });
}

    public function update(int $studentId, array $data): void
    {
        DB::transaction(function () use ($studentId, $data) {
            // 1) User
            $user = User::findOrFail($studentId);
            $user->name        = $data['name'];
            $user->fname       = $data['fname'];
            $user->mname       = $data['mname'];
            $user->mobile      = $data['mobile'];
            $user->address     = $data['address'];
            $user->gender      = $data['gender'];
            $user->religion    = $data['religion'];
            $user->dob         = date('Y-m-d', strtotime($data['dob']));
            $user->lob         = $data['lob'];
            $user->f_no        = $data['f_no'];
            $user->statusclass = $data['statusclass'];

            if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                if (!empty($user->image)) {
                    @unlink(public_path('upload/student_images/'.$user->image));
                }
                $filename = date('YmdHi').$data['image']->getClientOriginalName();
                $data['image']->move(public_path('upload/student_images'), $filename);
                $user->image = $filename;
            }
            $user->save();

            // 2) AssignStudent (par id caché dans le form)
            $assign = AssignStudent::where('id', $data['id'])
                ->where('student_id', $studentId)
                ->firstOrFail();

            $assign->year_id  = $data['year_id'];
            $assign->class_id = $data['class_id'];
            $assign->group_id = $data['group_id'];
            $assign->shift_id   = $data['shift_id'] ?? null;
            $assign->save();

            // 3) Discount
            $discount = DiscountStudent::firstOrNew(['assign_student_id' => $assign->id]);
            $discount->fee_category_id = 1;
            $discount->discount        = $data['discount'] ?? 0;
            $discount->save();
        });
    }

    public function promote(int $studentId, array $data): void
    {
        DB::transaction(function () use ($studentId, $data) {
            // 1) Update User (infos peuvent évoluer)
            $user = User::findOrFail($studentId);
            $user->name     = $data['name'];
            $user->fname    = $data['fname'];
            $user->mname    = $data['mname'];
            $user->mobile   = $data['mobile'];
            $user->address  = $data['address'];
            $user->gender   = $data['gender'];
            $user->religion = $data['religion'];
            $user->dob      = date('Y-m-d', strtotime($data['dob']));
            $user->lob      = $data['lob'];
            $user->f_no     = $data['f_no'];

            if (!empty($data['image']) && $data['image'] instanceof UploadedFile) {
                if (!empty($user->image)) {
                    @unlink(public_path('upload/student_images/'.$user->image));
                }
                $filename = date('YmdHi').$data['image']->getClientOriginalName();
                $data['image']->move(public_path('upload/student_images'), $filename);
                $user->image = $filename;
            }
            $user->save();

            // 2) Nouvelle affectation (nouvelle ligne AssignStudent)
            $assign = new AssignStudent();
            $assign->student_id = $studentId;
            $assign->year_id    = $data['year_id'];
            $assign->class_id   = $data['class_id'];
            $assign->group_id   = $data['group_id'];
            $assign->shift_id   = $data['shift_id'];
            $assign->save();

            // 3) Discount pour la nouvelle affectation
            $discount = new DiscountStudent();
            $discount->assign_student_id = $assign->id;
            $discount->fee_category_id   = 1;
            $discount->discount          = $data['discount'] ?? 0;
            $discount->save();
        });
    }

    /**
     * Supprime l'élève + toutes ses données liées.
     * Idéalement, les FK en DB ont onDelete('cascade').
     * Ici on nettoie aussi le fichier image et on double-sécurise.
     */
    public function deleteCompletely(int $studentId): void
    {
        DB::transaction(function () use ($studentId) {
            $user = User::findOrFail($studentId);

            // supprimer fichiers
            if (!empty($user->image)) {
                @unlink(public_path('upload/student_images/'.$user->image));
            }

            // si pas de FK cascade, on “nettoie” manuellement
            $assigns = \App\Models\AssignStudent::where('student_id', $studentId)->get();
            foreach ($assigns as $assign) {
                DiscountStudent::where('assign_student_id', $assign->id)->delete();
            }
            \App\Models\AssignStudent::where('student_id', $studentId)->delete();

            // autres tables par student_id
            StudentAbsence::where('student_id', $studentId)->delete();
            StudentMarks::where('student_id', $studentId)->delete();

            // user
            $user->delete();
        });
    }
}
