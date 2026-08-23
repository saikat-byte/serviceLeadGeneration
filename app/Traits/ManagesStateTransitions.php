<?php

namespace App\Traits;

use App\Models\StateTransition;
use App\Models\BusinessEvent;
use App\Exceptions\IllegalStateTransitionException;
use Illuminate\Support\Facades\DB;

trait ManagesStateTransitions
{
    public function transitionState($newState, string $eventName, ?int $actorId = null, ?string $reason = null, ?array $metadata = null)
    {
        DB::transaction(function () use ($newState, $eventName, $actorId, $reason, $metadata) {
            
            // Get current state (handle null/empty for newly created records)
            $fromState = $this->status instanceof \UnitEnum ? $this->status->value : $this->status;
            $fromState = $fromState ?? 'draft'; // Fallback to 'draft' if empty

            $toState = $newState instanceof \UnitEnum ? $newState->value : $newState;

            if ($fromState === $toState) {
                return;
            }

            //  ILLEGAL TRANSITION GUARD
            if (method_exists($this, 'allowedTransitions')) {
                $allowed = $this->allowedTransitions()[$fromState] ?? [];
                
                // Allow transition if fromState is effectively new/draft or in allowed list
                if (!in_array($toState, $allowed) && $fromState !== 'draft') {
                    throw IllegalStateTransitionException::forEntity($this, $fromState, $toState);
                }
            }

            $this->update(['status' => $newState]);

            StateTransition::create([
                'entity_type' => $this->getMorphClass(),
                'entity_id'   => $this->id,
                'from_state'  => $fromState,
                'to_state'    => $toState,
                'event'       => $eventName,
                'actor_id'    => $actorId,
                'reason'      => $reason,
                'metadata'    => $metadata,
            ]);

            BusinessEvent::create([
                'event_name'  => $eventName,
                'entity_type' => $this->getMorphClass(),
                'entity_id'   => $this->id,
                'actor_id'    => $actorId,
                'from_state'  => $fromState,
                'to_state'    => $toState,
                'payload'     => $metadata,
            ]);
        });
    }
}