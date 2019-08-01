class EnemyVisualMainNpcType extends EnemyVisualMain {

    constructor(enemyvisual) {
        super(enemyvisual);

        this.iconName = 'humanoid';
        // Set the icon initially to draw the current npc
        this._updateIconName();

        // Register to see if this enemy has any changes to its MDT connected states
        this.enemyvisual.enemy.register('mdt_connected', this, this._refresh.bind(this));
    }

    _getValidIconNames() {
        return [
            'aberration',
            'beast',
            'demon',
            'dragonkin',
            'elemental',
            'giant',
            'humanoid',
            'mechanical',
            'undead'
        ];
    }

    _getTemplateData() {
        console.assert(this instanceof EnemyVisualMainNpcType, this, 'this is not an EnemyVisualMainNpcType!');

        let mainVisualOuterClasses = ['enemy_icon_npc_type'];
        let mainVisualInnerClasses = ['enemy_icon', this.iconName];

        // Handle Teeming display
        if (this.enemyvisual.enemy.teeming === 'visible' || this.enemyvisual.enemy.teeming === 'hidden') {
            mainVisualOuterClasses.push('teeming');
        }
        // Handle beguiling display
        if (this.enemyvisual.enemy.isBeguiling()) {
            mainVisualInnerClasses.push('beguiling');
        }

        let npc = this.enemyvisual.enemy.npc;
        if (npc !== null) {
            mainVisualOuterClasses.push(npc.aggressiveness);

            // Enchanted Emissary
            if (npc.id === 155432) {
                mainVisualInnerClasses.push('enchanted');
            } else if (npc.id === 155433) {
                mainVisualInnerClasses.push('void');
            } else if (npc.id === 155434) {
                mainVisualInnerClasses.push('tide');
            }
        }

        // Any additional classes to add for when the enemy is selectable
        let selectionClasses = [];
        if (this.enemyvisual.enemy.isSelectable()) {
            selectionClasses.push('selected_enemy_icon');
        }

        return {
            // Set the main icon
            main_visual_outer_classes: mainVisualOuterClasses.join(' '),
            main_visual_inner_classes: mainVisualInnerClasses.join(' '),
            selection_classes: selectionClasses.join(' ')
        };
    }

    /**
     * Updates the iconName property based on the enemy's current NPC.
     * @private
     */
    _updateIconName() {
        let npc = this.enemyvisual.enemy.npc;
        if (npc !== null) {
            this.iconName = npc.type.type.toLowerCase();
        }
    }

    /**
     * The NPC on the enemy has been refreshed; rebuild the visual to match.
     */
    _refreshNpc() {
        console.assert(this instanceof EnemyVisualMainNpcType, this, 'this is not an EnemyVisualMainNpcType!');

        this._refresh();
    }

    /**
     * Refreshes the visual.
     * @private
     */
    _refresh() {
        console.assert(this instanceof EnemyVisualMainNpcType, this, 'this is not an EnemyVisualMainNpcType!');
        // Update the icon to a new icon as necessary
        this._updateIconName();
        // Re-draw the visual
        this.setIcon(this.iconName);
    }

    // getSize() {
    //     console.assert(this instanceof EnemyVisualMainNpcType, this, 'this is not an EnemyVisualMainNpcType!');
    //
    //     return this.iconName === 'boss' ? _bigIcon : _smallIcon;
    // }

    cleanup() {
        super.cleanup();
    }
}