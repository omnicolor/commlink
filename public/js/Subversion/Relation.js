'use strict';

class Relation {
    constructor(name, faction, level, notes) {
        this.archetypes = [];
        this.aspects = [];
        this.faction = faction;
        this.name = name;
        this.level = level;
        this.power = level.power;
        this.regard = level.regard;
        this.notes = notes;
        this.skills = [];
    }

    cost() {
        let cost = this.level.cost;
        cost = cost + (this.power - this.level.power) * 5;
        cost = cost + (this.regard - this.level.regard) * 2;

        this.aspects.forEach(function (aspect) {
            switch (aspect.id) {
                case 'adversarial':
                    const regardDifference = this.level.regard - this.regard;
                    cost = Math.max(cost - regardDifference * 2, 0);
                    break;
                case 'dues':
                    cost = cost / 2;
                    break;
                case 'multi-talented':
                    const extraSkills = Math.max(this.skills.length - 1, 0);
                    const extraArchetypes = Math.max(this.archetypes.length - 1, 0);
                    cost = cost + (cost * (extraSkills + extraArchetypes) / 2);
                    break;
                case 'supportive':
                    cost = cost + 15;
                    break;
                case 'toxic':
                    cost = Math.max(cost - 5, 1);
                    break;
            }
        }, this);
        return Math.ceil(cost);
    }
}
