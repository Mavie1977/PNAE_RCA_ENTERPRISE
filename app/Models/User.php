<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_RESPONSABLE = 'responsable';
    public const ROLE_AGENT = 'agent';
    public const ROLE_CITOYEN = 'citoyen';


public function agentProfile(): HasOne
{
    return $this->hasOne(AgentProfile::class);
}
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'active',
        'ministry_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public static function roles(): array
    {
        return [
            self::ROLE_CITOYEN => 'Citoyen',
            self::ROLE_AGENT => 'Agent public',
            self::ROLE_RESPONSABLE => 'Responsable ministériel',
            self::ROLE_ADMIN => 'Administrateur national',
        ];
    }

public function careerHistories(): HasMany
{
    return $this->hasMany(
        AgentCareerHistory::class,
        'agent_id'
    )->latest('effective_at');
}

public function advancements()
{
    return $this->hasMany(
        AgentAdvancement::class,
        'agent_id'
    );
}

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isResponsable(): bool
    {
        return $this->role === self::ROLE_RESPONSABLE;
    }

    public function isAgent(): bool
    {
        return $this->role === self::ROLE_AGENT;
    }

    public function isCitoyen(): bool
    {
        return $this->role === self::ROLE_CITOYEN;
    }

    public function belongsToMinistry(?int $ministryId): bool
    {
        return $ministryId !== null
            && (int) $this->ministry_id === $ministryId;
    }
	public function isResponsableFonctionPublique(): bool
{
    if ($this->role !== self::ROLE_RESPONSABLE) {
        return false;
    }

    return $this->ministry()
        ->where('code', 'FONCTION_PUBLIQUE')
        ->exists();
}

public function leaves()
{
    return $this->hasMany(
        AgentLeave::class,
        'agent_id'
    );
}

public function disciplinaryActions()
{
    return $this->hasMany(
        AgentDisciplinaryAction::class,
        'agent_id'
    );
}
public function rhDocuments()
{
    return $this->hasMany(
        AgentRhDocument::class,
        'agent_id'
    );
}

public function trainings()
{
    return $this->hasMany(
        AgentTraining::class,
        'agent_id'
    );
}

public function skills()
{
    return $this->hasMany(
        AgentSkill::class,
        'agent_id'
    );
}

}