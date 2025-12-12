@php
$prefix = Request::route()->getPrefix();
$route = Route::current()->getName();
$user = Auth::user(); // Get the authenticated user
@endphp

<aside class="main-sidebar">
    <section class="sidebar"> 
        <div class="user-profile">
            <div class="ulogo">
                <a href="#">
                    <div class="d-flex align-items-center justify-content-center">            
                        <img src="{{ asset('backend/images/grapmult_logo.png') }}" alt="">
                        <h3><b>Grapmult</b> AGE</h3>
                    </div>
                </a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">  

            {{-- Common link for all roles --}}
            @if($user->role == 'Admin' || $user->role == 'Operator')
            <li class="{{ ($route == 'dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i data-feather="pie-chart"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>
            @elseif($user->usertype == 'Employé')
            <li class="{{ ($route == 'teacher.dashboard') ? 'active' : '' }}">
                <a href="{{ route('teacher.dashboard') }}">
                    <i data-feather="pie-chart"></i>
                    <span>Tableau de Bord Enseignant</span>
                </a>
            </li>
            @else
              <li class="{{ ($route == 'admin.logout') ? 'active' : '' }}">
                <a href="{{ route('admin.logout') }}">
                    <i data-feather="pie-chart"></i>
                    <span>Tableau de Bord Enseignant</span>
                </a>
            </li>
            @endif

            {{-- Admin only: Gerer les Utilisateurs --}}
            @if($user->role == 'Admin')
                <li class="treeview {{ ($prefix == '/users') ? 'active' : '' }}">
                    <a href="#">
                        <i data-feather="message-circle"></i>
                        <span>Gerer les Utilisateurs</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ ($route == 'user.view') ? 'active' : '' }}">
                            <a href="{{ route('user.view') }}"><i class="ti-more"></i>Utilisateurs</a>
                        </li>
                        <li class="{{ ($route == 'users.add') ? 'active' : '' }}">
                            <a href="{{ route('users.add') }}"><i class="ti-more"></i>Ajouter Utilisateur</a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Gestion de Profil: Available to both Admin, Operator and Teacher --}}
            @if($user->role == 'Admin' || $user->role == 'Operator' || $user->usertype == 'Employé')
                <li class="treeview {{ ($prefix == '/profile') ? 'active' : '' }}">
                    <a href="#">
                        <i data-feather="mail"></i>
                        <span>Gestion de Profil</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ ($route == 'profile.view') ? 'active' : '' }}">
                            <a href="{{ route('profile.view') }}"><i class="ti-more"></i>Votre Profil</a>
                        </li>

                        <!-- Accessible only by Admin and Operator -->
                        @if($user->role == 'Admin' || $user->role == 'Operator')
                        <li class="{{ ($route == 'profile.password.view') ? 'active' : '' }}">
                            <a href="{{ route('profile.password.view') }}"><i class="ti-more"></i>Modifier le Mot de Passe</a>
                        </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- Gestion de La Configuration: Available to both Admin and Operator --}}
            @if($user->role == 'Admin' || $user->role == 'Operator')
                <li class="treeview {{ ($prefix == '/setups') ? 'active' : '' }}">
                    <a href="#">
                        <i data-feather="credit-card"></i>
                        <span>Gestion de La Configuration</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ ($route == 'student.class.view') ? 'active' : '' }}">
                            <a href="{{ route('student.class.view') }}"><i class="ti-more"></i>Classes des Élèves</a>
                        </li>
                        <!-- Add more configuration menu items as needed -->
                        <li class="{{ ($route == 'student.year.view')?'active':'' }}"><a href="{{ route('student.year.view') }}"><i class="ti-more"></i>Année Scolaire</a></li>
                       <li class="{{ ($route == 'student.group.view')?'active':'' }}"><a href="{{ route('student.group.view') }}"><i class="ti-more"></i>Groupe</a></li>
                       <li class="{{ ($route == 'student.shift.view')?'active':'' }}"><a href="{{ route('student.shift.view') }}"><i class="ti-more"></i>Passage</a></li>
                       <li class="{{ ($route == 'fee.category.view')?'active':'' }}"><a href="{{ route('fee.category.view') }}"><i class="ti-more"></i>Catégorie Des Frais</a></li>
                      <li class="{{ ($route == 'fee.amount.view')?'active':'' }}"><a href="{{ route('fee.amount.view') }}"><i class="ti-more"></i>Montant de la Catégorie de Frais</a></li>
                      <li class="{{ ($route == 'term.type.view')?'active':'' }}"><a href="{{ route('term.type.view') }}"><i class="ti-more"></i>Trimestre/Semestre</a></li>
                       <li class="{{ ($route == 'exam.type.view')?'active':'' }}"><a href="{{ route('exam.type.view') }}"><i class="ti-more"></i>Type d'Examen</a></li>
                       <li class="{{ ($route == 'assign.exam.view')?'active':'' }}"><a href="{{ route('assign.exam.view') }}"><i class="ti-more"></i>Affecter Type Examen à Trimestre/Semestre </a></li>
                       <li class="{{ ($route == 'school.subject.view')?'active':'' }}"><a href="{{ route('school.subject.view') }}"><i class="ti-more"></i>Matières</a></li>
                        <li class="{{ ($route == 'assign.subject.view')?'active':'' }}"><a href="{{ route('assign.subject.view') }}"><i class="ti-more"></i>Affecter Une Matière (Classe)</a></li>
                        <li class="{{ ($route == 'designation.view')?'active':'' }}"><a href="{{ route('designation.view') }}"><i class="ti-more"></i>Désignation </a></li>
                        <li class="{{ ($route == 'assign.designation.view')?'active':'' }}"><a href="{{ route('assign.designation.view') }}"><i class="ti-more"></i>Affecter Prof à Désignation </a></li>
                        <li class="{{ ($route == 'assign.subject.teacher.view')?'active':'' }}"><a href="{{ route('assign.subject.teacher.view') }}"><i class="ti-more"></i>Affecter Prof. à Une Classe </a></li>
          
                    </ul>
                </li>

                <li class="treeview {{ ($prefix == '/students')?'active':'' }}">
                <a href="#">
                   <i data-feather="hard-drive"></i></i> <span>Gestion des Élèves</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-right pull-right"></i>
                  </span>
                </a>
                <ul class="treeview-menu">
              <li class="{{ ($route == 'student.registration.view')?'active':'' }}"><a href="{{ route('student.registration.view') }}"><i class="ti-more"></i>Inscription des Élèves</a></li>

                <li class="{{ ($route == 'roll.generate.view')?'active':'' }}"><a href="{{ route('roll.generate.view') }}"><i class="ti-more"></i>Régistres Des Élèves</a></li>
                <li class="{{ ($route == 'student.absence.view')?'active':'' }}"><a href="{{ route('student.absence.view') }}"><i class="ti-more"></i>Abscences Des Élèves</a></li>
                 <li class="{{ ($route == 'registration.fee.view')?'active':'' }}"><a href="{{ route('registration.fee.view') }}"><i class="ti-more"></i>Frais d'Inscription </a></li>
                 <li class="{{ ($route == 'monthly.fee.view')?'active':'' }}"><a href="{{ route('monthly.fee.view') }}"><i class="ti-more"></i>Frais Mensuels </a></li>
                 <li class="{{ ($route == 'exam.fee.view')?'active':'' }}"><a href="{{ route('exam.fee.view') }}"><i class="ti-more"></i>Frais d'Examen </a></li>

               
         
            
          </ul>
        </li>
            @endif

            @if(Auth::user()->role=='Admin') 
            <li class="treeview {{ ($prefix == '/employees')?'active':'' }}">
          <a href="#">
            <i data-feather="package"></i> <span>Gestion des Employés</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
        
        <li  class="{{ ($route == 'employee.registration.view')?'active':'' }}"><a href="{{ route('employee.registration.view') }}"><i class="ti-more"></i>Inscription des Employés</a></li>
         <li  class="{{ ($route == 'employee.salary.view')?'active':'' }}"><a href="{{ route('employee.salary.view') }}"><i class="ti-more"></i>Salaires des Employés</a></li>
         <li class="{{ ($route == 'employee.leave.view')?'active':'' }}"><a href="{{ route('employee.leave.view') }}"><i class="ti-more"></i>Abandons de Poste</a></li>
          <li class="{{ ($route == 'employee.attendance.view')?'active':'' }}"><a href="{{ route('employee.attendance.view') }}"><i class="ti-more"></i>Présence des employés</a></li>
           <li class="{{ ($route == 'employee.monthly.salary')?'active':'' }}"><a href="{{ route('employee.monthly.salary') }}"><i class="ti-more"></i>Salaire Mensuel des Employés</a></li>
           </ul>
        </li>
          @endif

            {{-- Employé (Teacher): Gestion des Notes de Classe only --}}
            @if($user->role == 'Admin' || $user->role == 'Operator' || $user->usertype == 'Employé')
                <li class="treeview {{ ($prefix == '/marks') ? 'active' : '' }}">
                    <a href="#">
                        <i data-feather="edit-2"></i>
                        <span>Gestion Des Notes de Classe</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ ($route == 'marks.manage') ? 'active' : '' }}">
                            <a href="{{ route('marks.manage') }}"><i class="ti-more"></i>Saisir les Notes de Classe</a>
                        </li>
                        {{-- <li class="{{ ($route == 'marks.entry.add') ? 'active' : '' }}">
                            <a href="{{ route('marks.entry.add') }}"><i class="ti-more"></i>Saisir les Notes de Classe</a>
                        </li> --}}
                        {{-- <li class="{{ ($route == 'marks.entry.edit') ? 'active' : '' }}">
                            <a href="{{ route('marks.entry.edit') }}"><i class="ti-more"></i>Modifier les Notes</a>
                        </li>
                        <li class="{{ ($route == 'marks.entry.grade') ? 'active' : '' }}">
                            <a href="{{ route('marks.entry.grade') }}"><i class="ti-more"></i>Appréciation Des Notes</a>
                        </li> --}}
                    </ul>
                </li>
            @endif

            {{-- Admin-only: Gestion des Comptes --}}
            @if($user->role == 'Admin')
                <li class="treeview {{ ($prefix == '/accounts') ? 'active' : '' }}">
                    <a href="#">
                        <i data-feather="inbox"></i>
                        <span>Gestion Des Comptes</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="{{ ($route == 'student.fee.view') ? 'active' : '' }}">
                            <a href="{{ route('student.fee.view') }}"><i class="ti-more"></i>Frais Scolaire</a>
                        </li>
                        <li class="{{ ($route == 'account.salary.view') ? 'active' : '' }}">
                            <a href="{{ route('account.salary.view') }}"><i class="ti-more"></i>Salaire Des Employé(e)s</a>
                        </li>
                        <!-- Add more account-related menu items as needed -->
                        <li class="{{ ($route == 'other.cost.view')?'active':'' }}"><a href="{{ route('other.cost.view') }}"><i class="ti-more"></i>Autres Frais</a></li>

                    </ul>
                </li>

                <li class="header nav-small-cap">Interface Des Rapports</li>
      
          <li class="treeview {{ ($prefix == '/reports')?'active':'' }}">
          <a href="#">
            <i data-feather="server"></i></i> <span> Gestion Des Rapports</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          <li class="{{ ($route == 'monthly.profit.view')?'active':'' }}"><a href="{{ route('monthly.profit.view') }}"><i class="ti-more"></i>Profit Mensuel-Annuel</a></li> 
          <li class="{{ ($route == 'marksheet.generate.view')?'active':'' }}"><a href="{{ route('marksheet.generate.view') }}"><i class="ti-more"></i>Gestion des Bulletins par Eleve</a></li>
          <li class="{{ ($route == 'admin.reports.bulletin.view')?'active':'' }}"><a href="{{ route('admin.reports.bulletin.view') }}"><i class="ti-more"></i>Gestion des Bulletins par Classe</a></li>
          <li class="{{ ($route == 'student.result.view')?'active':'' }}"><a href="{{ route('admin.reports.statistics.index') }}"><i class="ti-more"></i>Gestions Des Statistiques par Classe</a></li>
          <li class="{{ ($route == 'attendance.report.view')?'active':'' }}"><a href="{{ route('attendance.report.view') }}"><i class="ti-more"></i>Rapport des Présences</a></li>
          <li class="{{ ($route == 'student.result.view')?'active':'' }}"><a href="{{ route('student.result.view') }}"><i class="ti-more"></i>Résultats Des Élèves </a></li>
           <li class="{{ ($route == 'student.idcard.view')?'active':'' }}"><a href="{{ route('student.idcard.view') }}"><i class="ti-more"></i>Gestion De Carte Scolaires</a></li> 
           </ul>
        </li>

        <li class="treeview {{ ($prefix == '/report')?'active':'' }}">
          <a href="#">
            <i data-feather="server"></i></i> <span> BULLETINS</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          
           <li class="{{ ($route == 'report.bulletins.index')?'active':'' }}"><a href="{{ route('report.bulletins.index') }}"><i class="ti-more"></i>Gestion De Carte Scolaires</a></li> 
           </ul>
        </li>

            @endif
           <!-- <li class="header nav-small-cap">Interface de l'Utilisateur</li>
      
        <li class="treeview">
          <a href="#">
            <i data-feather="grid"></i>
            <span>Components</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-right pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="components_alerts.html"><i class="ti-more"></i>Alerts</a></li>
            <li><a href="components_badges.html"><i class="ti-more"></i>Badge</a></li>
            
          </ul>
        </li> -->

        </ul>
    </section>
    <div class="sidebar-footer">
    <!-- item-->
    <a href="javascript:void(0)" class="link" data-toggle="tooltip" title="" data-original-title="Settings" aria-describedby="tooltip92529"><i class="ti-settings"></i></a>
    <!-- item-->
    <a href="mailbox_inbox.html" class="link" data-toggle="tooltip" title="" data-original-title="Email"><i class="ti-email"></i></a>
    <!-- item-->
    <a href="javascript:void(0)" class="link" data-toggle="tooltip" title="" data-original-title="Logout"><i class="ti-lock"></i></a>
  </div>
</aside>
