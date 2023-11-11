<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'smAppTemplate';

    public $timestamps = true;

    // Constants representing user roles
    const ROLE_ADMIN = 1;
    const ROLE_EDITOR = 2;
    const ROLE_CONTROLLERSHIP = 3;
    const ROLE_USER = 4;
    const ROLE_PARTNER = 5;

    // Capabilities for each role
    const CAPABILITIES = [
        self::ROLE_ADMIN => ['manage', 'edit', 'controllership', 'view'],
        self::ROLE_EDITOR => ['edit', 'view'],
        self::ROLE_CONTROLLERSHIP => ['controllership', 'view'],
        self::ROLE_USER => ['partial_view'],
        self::ROLE_PARTNER => ['view'],
    ];

    const CAPABILITY_TRANSLATIONS = [
        'manage' => 'Configurações Gerais',
        'edit' => 'Editar Metas',
        'controllership' => 'Editar Vistorias',
        'view' => 'Visualização Íntegral',
        'partial_view' => 'Visualização Limitada',
    ];

    /**
     * Get the human-readable capability.
     *
     * @param string $capability The capability key.
     * @return string The human-readable capability.
     */
    public static function getHumanReadableCapability($capability)
    {
        return self::CAPABILITY_TRANSLATIONS[$capability] ?? ucfirst(str_replace('_', ' ', $capability));
    }

    // Attributes that are mass assignable
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        //'status',
        //'avatar',
        //'cover',
    ];

    // Attributes that should be hidden for arrays
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Attributes that should be cast to native types
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the human-readable name of the user's role.
     *
     * @param int $role The role identifier.
     * @return string The human-readable name of the role.
     */
    public function getRoleName($role)
    {
        $roles = [
            self::ROLE_ADMIN => 'Administração',
            self::ROLE_EDITOR => 'Gerência',
            self::ROLE_CONTROLLERSHIP => 'Controladoria',
            self::ROLE_USER => 'Operacional',
            self::ROLE_PARTNER => 'Sócio Investidor',
        ];

        return $roles[$role] ?? 'Função Desconhecida';
    }

    /**
     * Check if the user has a specific role.
     *
     * @param int $role The role identifier.
     * @return bool True if the user has the role, false otherwise.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }


    public function hasAnyRole(...$roles) {
        return in_array($this->role, $roles);
    }

    /**
     * Check if the user has a specific capability.
     *
     * @param string $capability The capability to check for.
     * @return bool True if the user has the capability, false otherwise.
     */
    public function hasCapability($capability)
    {
        return in_array($capability, self::CAPABILITIES[$this->role] ?? []);
    }


    /**
     * Generate a permissions table based on roles and capabilities.
     *
     * @return string HTML representation of the permissions table.
     */
    public static function generatePermissionsTable()
    {
        $roles = array_keys(self::CAPABILITIES);
        $capabilitiesList = ['manage', 'edit', 'controllership', 'view', 'partial_view'];

        $caption = '<div class="row">';
            $caption .= '<div class="col text-end fw-normal fs-12"><i class="ri-checkbox-circle-fill text-success me-1 align-bottom" title="Ok"></i> Acesso permitido</div>';
            //$caption .= '<div class="col fw-normal fs-12"><i class="ri-error-warning-line text-warning me-1 align-bottom" title="Limitações Administrativas"></i> Limitado pela Atribuição</div>';
            $caption .= '<div class="col text-start fw-normal fs-12"><i class="ri-close-circle-line text-danger me-1 align-bottom" title="Não permitido"></i> Não permitido</div>';
            //$caption .= '<div class="col fw-normal fs-12"><i class="ri-forbid-2-line text-info me-1 align-bottom" title="Somente visualização"></i> Somente visualização</div>';
        $caption .= '</div>';

        $html = '<div class="card mt-2">';
            $html .= '<div class="card-header fw-bold text-uppercase">Níveis e Permissões</div>';
            $html .= '<div class="card-body">';
                $html .= '<div class="table-responsive">';
                    $html .= '<table class="table table-bordered table-striped mb-0">';
                        $html .= '<thead class="table-light">';
                            $html .= '<tr>';
                                $html .= '<th class="fw-normal">'.$caption.'</th>';

                                foreach ($roles as $roleId) {
                                    $html .= '<th class="text-center">' . (new self)->getRoleName($roleId) . '</th>';
                                }

                            $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody>';

                        foreach ($capabilitiesList as $capability) {
                            $html .= '<tr>';
                            //$html .= '<td class="text-end">' . ucfirst(str_replace('_', ' ', $capability)) . ':</td>';
                            $html .= '<td class="text-end">' . self::getHumanReadableCapability($capability) . ':</td>';

                            foreach ($roles as $roleId) {
                                $html .= '<td class="text-center">';
                                if (in_array($capability, self::CAPABILITIES[$roleId])) {
                                    $html .= '<i class="ri-checkbox-circle-fill text-success" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Permitido" data-bs-original-title="Permitido"></i>';
                                }
                                /*
                                else if ($roleId == 4) {
                                    $html .= '<i class="ri-error-warning-line text-warning" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Limitado conforme a Atribuição" data-bs-original-title="Limitado conforme a Atribuição"></i>';
                                }
                                */
                                else {
                                    $html .= '<i class="ri-close-circle-line text-danger" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Não permitido" data-bs-original-title="Não permitido"></i>';
                                }
                                $html .= '</td>';
                            }

                            $html .= '</tr>';
                        }

                        $html .= '</tbody>';
                    $html .= '</table>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


}
