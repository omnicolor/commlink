function Points(character) {
    this.activeSkills = 0;
    this.attributes = 0;
    this.character = character;
    this.contacts = 0;
    this.forms = 0;
    this.karma = 25;
    this.knowledgeSkills = 0;
    this.magics = 0;
    this.resources = 0;
    this.special = 0;
    this.skillGroups = 0;

    this.updatePriorities = function () {
        if (!this.character.priorities) {
            return;
        }
        const priorities = this.character.priorities;
        switch (priorities.a) {
            case 'metatype':
                switch (priorities.metatype) {
                    case 'human':
                        this.special = 9;
                        break;
                    case 'elf':
                        this.special = 8;
                        break;
                    case 'dwarf':
                    case 'ork':
                        this.special = 7;
                        break;
                    case 'troll':
                        this.special = 5;
                        break;
                }
                break;
            case 'attributes':
                this.attributes = 24;
                break;
            case 'skills':
                this.activeSkills = 46;
                this.skillGroups = 10;
                break;
            case 'resources':
                switch (priorities.gameplay) {
                    case 'established':
                    default:
                        this.resources = 450000;
                        break;
                    case 'prime':
                        this.resources = 500000;
                        break;
                    case 'street':
                        this.resources = 75000;
                        break;
                }
            case 'magic':
                if ('magician' === priorities.magic) {
                    this.magics = 10;
                } else if ('technomancer' === priorities.magic) {
                    this.forms = 7;
                }
                break;
        }

        switch (priorities.b) {
            case 'metatype':
                switch (priorities.metatype) {
                    case 'elf':
                        this.special = 6;
                        break;
                    case 'human':
                        this.special = 7;
                        break;
                    case 'dwarf':
                    case 'ork':
                        this.special = 4;
                        break;
                    case 'troll':
                        this.special = 0;
                        break;
                }
                break;
            case 'attributes':
                this.attributes = 20;
                break;
            case 'skills':
                this.activeSkills = 36;
                this.skillGroups = 5;
                break;
            case 'resources':
                switch (priorities.gameplay) {
                    case 'established':
                        this.resources = 275000;
                        break;
                    case 'prime':
                        this.resources = 325000;
                        break;
                    case 'street':
                        this.resources = 50000;
                        break;
                }
            case 'magic':
                switch (priorities.magic) {
                    case 'magician':
                        // Drop through
                    case 'mystic':
                        this.magics = 7;
                        break;
                    case 'technomancer':
                        this.forms = 4;
                        break;
                }
                break;
        }

        switch (priorities.c) {
            case 'metatype':
                switch (priorities.metatype) {
                    case 'dwarf':
                        this.special = 1;
                        break;
                    case 'elf':
                        this.special = 3;
                        break;
                    case 'human':
                        this.special = 5;
                        break;
                    case 'ork':
                        this.special = 0;
                        break;
                }
                break;
            case 'attributes':
                this.attributes = 16;
                break;
            case 'skills':
                this.activeSkills = 28;
                this.skillGroups = 2;
                break;
            case 'resources':
                switch (priorities.gameplay) {
                    case 'established':
                        this.resources = 140000;
                        break;
                    case 'prime':
                        this.resources = 210000;
                        break;
                    case 'street':
                        this.resources = 25000;
                        break;
                }
                break;
            case 'magic':
                switch (priorities.magic) {
                    case 'magician':
                    case 'mystic':
                        this.magics = 5;
                        break;
                    case 'technomancer':
                        this.forms = 3;
                        break;
                }
                break;
        }

        switch (priorities.d) {
            case 'metatype':
                switch (priorities.metatype) {
                    case 'elf':
                        this.special = 0;
                        break;
                    case 'human':
                        this.special = 3;
                        break;
                }
                break;
            case 'attributes':
                this.attributes = 14;
                break;
            case 'skills':
                this.activeSkills = 22;
                this.skillGroups = 0;
                break;
            case 'resources':
                switch (priorities.gameplay) {
                    case 'established':
                        this.resources = 50000;
                        break;
                    case 'prime':
                        this.resources = 150000;
                        break;
                    case 'street':
                        this.resources = 15000;
                        break;
                }
        }

        switch (priorities.e) {
            case 'metatype':
                this.special = 1;
                break;
            case 'attributes':
                this.attributes = 12;
                break;
            case 'skills':
                this.activeSkills = 18;
                this.skillGroups = 0;
                break;
            case 'resources':
                switch (priorities.gameplay) {
                    case 'established':
                        this.resources = 6000;
                        break;
                    case 'prime':
                        this.resources = 100000;
                        break;
                    case 'street':
                        this.resources = 6000;
                        break;
                }
        }
    };

    this.updateAttributes = function () {
        /**
         * Map of races to their non-standard normal attributes.
         * @type {!Object}
         */
        let raceLimits = {
            dwarf: {
                body: { max: 8, min: 3 },
                reaction: { max: 5, min: 1 },
                strength: { max: 8, min: 3 },
                willpower: { max: 7, min: 2 }
            },
            elf: {
                agility: { max: 7, min: 2 },
                charisma: { max: 8, min: 3 }
            },
            human: {},
            ork: {
                body: { max: 9, min: 4 },
                strength: { max: 8, min: 3 },
                logic: { max: 5, min: 1 },
                charisma: { max: 5, min: 1 }
            },
            troll: {
                body: { max: 10, min: 5 },
                agility: { max: 5, min: 1 },
                strength: { max: 10, min: 5 },
                logic: { max: 5, min: 1 },
                intuition: { max: 5, min: 1 },
                charisma: { max: 4, min: 1 }
            }
        };

        let min = 1;
        let max = 6;

        /** @type {!Array} */
        const commonAttributes = [
            'body', 'agility', 'reaction', 'strength', 'willpower', 'logic',
            'intuition', 'charisma'
        ];

        // Filter to just the metatype of the character.
        if (this.character.metatype in raceLimits) {
            raceLimits = raceLimits[metatype];
        } else {
            raceLimits = {};
        }

        let parent = this;
        $.each(commonAttributes, function (unused, attribute) {
            let value = parent.character[attribute];
            if (!value) {
                return;
            }
            // Default min and max.
            min = 1;
            max = 6;

            // Override min and max if the race has an override for that
            // attribute.
            if (attribute in raceLimits) {
                min = raceLimits[attribute].min;
                max = raceLimits[attribute].max;
            }

            // You don't earn points by going below the minimum.
            if (value < min) {
                value = min;
            }

            // Note that we don't disallow going over the max, this will allow
            // Exceptional Attribute quality to work.
            parent.attributes -= value - min;
        });

        // If they've spent extra attribute points, figure out which to improve
        // with karma.
        if (this.attributes < 0) {
            let minimumAttribute;
            let minimumAttributeValue;
            $.each(commonAttributes, function (unused, attribute) {
                const value = parent.character[attribute];
                if (!minimumAttribute || value < minimumAttributeValue) {
                    minimumAttribute = attribute;
                    minimumAttributeValue = value;
                }
            });
            let karmaCost = 0;
            while (this.attributes < 0) {
                karmaCost += minimumAttributeValue * 5;
                this.attributes++;
            }
            this.karma -= karmaCost;
        }
    };

    this.updateSpecialPoints = function () {
        const priorities = this.character.priorities;
        if (!priorities) {
            return;
        }

        let min = 1;

        // Edge must be between 1 and 6 for non-humans, 2 and 7 for humans.
        // Points up to the minimum are free.
        if ('human' === priorities.metatype) {
            min = 2;
        }
        const edge = this.character.edge;
        if (edge) {
            this.special -= this.character.edge - min;
        }

        // No reason to process further if they haven't chosen a magic type.
        if (!priorities.magic) {
            return;
        }

        // If the character is a technomancer, they may spend special points on
        // their resonance attribute, with the maximum depending on the priority
        // they chose for magic.
        if ('technomancer' === priorities.magic) {
            if ('magic' === priorities.a) {
                this.special -= (this.character.resonance - 6);
                return;
            }
            if ('magic' === priorities.b) {
                this.special -= (this.character.resonance - 4);
                return;
            }
            if ('magic' === priorities.c) {
                this.special -= (this.character.resonance - 3);
                return;
            }
            return;
        }

        // If the character is magic, they can spend special points on their
        // magic rating, with the maximum depending on the priority they chose
        // for magic and the type of magic they practice.
        if (priorities.a === 'magic' || 'A' === priorities.magicPriority) {
            this.special -= (this.character.magic - 6)
            return;
        }
        if (priorities.b === 'magic' || 'B' === priorities.magicPriority) {
            switch (priorities.magic) {
                case 'magician':
                case 'mystic':
                    this.special -= (this.character.magic - 4);
                    return;
                case 'adept':
                    this.special -= (this.character.magic - 6);
                    return;
                case 'aspected':
                    this.special -= (this.character.magic - 5);
                    return;
                default:
                    return;
            }
        }
        if (priorities.c === 'magic' || 'C' === priorities.magicPriority) {
            switch (priorities.magic) {
                case 'adept':
                    this.special -= (this.character.magic - 4);
                    return;
                case 'aspected':
                case 'magician':
                case 'mystic':
                    this.special -= (this.character.magic - 3);
                    return;
                default:
                    return;
            }
        }
        if (priorities.d === 'magic' || 'D' === priorities.magicPriority) {
            this.special -= (this.character.magic - 2);
        }
    };

    this.updateKarmaForQualities = function () {
        let parent = this;
        $.each(this.character.qualities, function (unused, quality) {
            parent.karma += quality.karma;
        });
    };

    this.updateKarmaForMartialArts = function () {
        const martialArts = character.martialArts;
        if (!martialArts || 0 === martialArts.styles.length) {
            return;
        }
        let parent = this;

        // The style itself costs seven karma.
        parent.karma -= 7;

        if (martialArts.techniques.length > 1) {
            // The first martial art technique is free.
            parent.karma -= (martialArts.techniques.length - 1) * 5;
        }
    };

    this.updateActiveSkills = function () {
        let parent = this;
        $.each(this.character.skills, function (unused, skill) {
            parent.activeSkills -= skill.level;
            if (skill.specialization) {
                parent.activeSkills -= 1;
            }
        });
    };

    this.updateSkillGroups = function () {
        let parent = this;
        $.each(this.character.skillGroups, function (group, level) {
            parent.skillGroups -= level;
        });

        if (this.skillGroups < 0) {
            for (let i = this.skillGroups; i; i++) {
                parent.karma += i * 5;
            }
        }
    };

    this.updateKnowledgeSkills = function () {
        const skills = this.character.knowledgeSkills;
        let points = (this.character.intuition + this.character.logic) * 2;
        let halveAcademic = false;
        let halveStreet = false;
        let halveProfessional = false;
        let halveLanguage = false;

        $.each(this.character.qualities, function (unused, quality) {
            if (quality.effects && quality.effects['knowledge-skill-points']) {
                points += quality.effects['knowledge-skill-points'];
            }

            if ('college-education' === quality.id) {
                halveAcademic = true;
            }

            if ('school-of-hard-knocks' === quality.id) {
                halveStreet = true;
            }

            if ('technical-school-education' === quality.id) {
                halveProfessional = true;
            }

            if ('linguist' === quality.id) {
                halveLanguage = true;
            }
        });

        $.each(skills, function (unused, skill) {
            if ('N' === skill.level) {
                return;
            }
            if (
                (halveAcademic && 'academic' === skill.category) ||
                (halveStreet && 'street' === skill.category) ||
                (halveProfessional && 'professional' === skill.category) ||
                (halveLanguage && 'language' === skill.category)
            ) {
                points -= Math.ceil(skill.level / 2);
                return;
            }
            points -= skill.level;
            if (skill.specialization) {
                points -= 1;
            }
        });

        this.knowledgeSkills = points;
    };

    this.updateNuyenFromAugmentations = function () {
        return;
        const cyberwareModifiers = {
            Alpha: 1.2,
            Beta: 1.5,
            Delta: 2.5,
            Standard: 1,
            Used: 0.75
        };
        $.each(this.character.augmentations, function (unused, augmentation) {
            if (!augmentation.grade) {
                augmentation.grade = 'Standard';
            }
            const modifier = cyberwareModifiers[augmentation.grade];
            this.resources -= Math.floor(augmentation.cost * modifier);
            if (augmentation.modifications && augmentation.modifications.length) {
                $.each(augmentation.modifications, function (unused, mod) {
                    this.resources -= Math.floor(mod.cost * modifier);
                });
            }
        });
    };

    this.updateNuyen = function () {
        this.updateNuyenFromAugmentations();

        $.each(this.character.weapons, function (unused, weapon) {
            if (!weapon) {
                return;
            }
            this.resources -= weapon.cost;
            if (weapon.addedAccessories) {
                $.each(weapon.addedAccessories, function (unused, mod) {
                    if (!mod) {
                        // Accessories are index by slot, so may be null.
                        return;
                    }
                    if (mod.cost) {
                        this.resources -= mod.cost;
                    } else {
                        this.resources -= weapon.cost *
                            (mod['cost-modifier'] - 1);
                    }
                });
            }
            if (weapon.addedModifications) {
                $.each(weapon.addedModifications, function (unused, mod) {
                    if (mod.cost) {
                        this.resources -= mod.cost;
                    } else {
                        this.resources -= weapon.cost *
                            (mod['cost-modifier'] - 1);
                    }
                });
            }
            if (weapon.ammo) {
                $.each(weapon.ammo, function (unused, ammo) {
                    if (ammo.contained) {
                        // Ammo is a full clip.

                        // Clips (or mags, or drums, or whatever) are 5 nuyen.
                        this.resources -= 5;

                        if (ammo.tracer) {
                            // Each tracer round costs 6 nuyen and they're
                            // loaded every third round.
                            this.resources -=
                                6 * Math.floor(ammo.quantity / 3);

                            // Which means the rest of the ammo is normal cost.
                            this.resources -= ammo.ammo.cost / 10 *
                                (ammo.quantity - Math.floor(ammo.quantity / 3));
                        } else {
                            this.resources -=
                                ammo.ammo.cost / 10 * ammo.quantity;
                        }
                    } else {
                        // Ammo is loose.
                        this.resources -=
                            ammo.ammo.cost / 10 * ammo.quantity;
                    }
                });
                this.resources = Math.floor(this.resources);
            }
        });
        $.each(this.character.armor, function (unused, armorItem) {
            if (!armorItem) {
                return;
            }
            this.ointsToSpend.resources -= armorItem.cost;
            if (armorItem.modifications) {
                $.each(armorItem.modifications, function (unused, mod) {
                    if (!mod) {
                        return;
                    }
                    if (mod.cost) {
                        this.ointsToSpend.resources -= mod.cost;
                    } else {
                        this.ointsToSpend.resources -=
                            armorItem.cost * (mod['cost-multiplier'] - 1);
                    }
                });
            }
        });
        $.each(this.character.gear, function (unused, item) {
            if (!item || item.quantity === 0) {
                return;
            }
            this.resources -= item.cost * item.quantity;
            if (item['programs-installed']) {
                $.each(item['programs-installed'], function (unused, program) {
                    this.resources -= program.cost;
                });
            }
            if (item.modifications) {
                $.each(item.modifications, function (unused, modification) {
                    this.resources -= modification.cost;
                });
            }
        });
        $.each(this.character.vehicles, function (unused, vehicle) {
            if (!vehicle) {
                return;
            }
            this.resources -= vehicle.cost;
            $.each(vehicle.equipment, function (unused, item) {
                if (!item) {
                    return;
                }
                this.resources -= item.cost;
            });
            $.each(vehicle.modifications, function (unused, mod) {
                if (!mod) {
                    return;
                }
                this.resources -= mod.cost;
            });
        });
        $.each(this.character.identities, function (unused, identity) {
            this.resources -= sr.calculateIdentityCost(identity);
        });

        if (this.resources < 0) {
            this.karma += Math.floor(this.resources / 2000);
        }
    };

    this.updateContactPoints = function () {
        const attributes = character.attributes;
        if (!attributes) {
            return;
        }

        let contactPoints = attributes.charisma * 3;
        $.each(this.character.contacts, function (unused, contact) {
            if (!contact) {
                return;
            }
            contactPoints -= contact.loyalty + contact.connection;
        });

        $.each(this.character.qualities, function (unused, quality) {
            if ('friends-in-high-places' === quality.id) {
                contactPoints += attributes.charisma * 4;
            }
        });

        this.contacts = contactPoints;
        if (contactPoints < 0) {
            this.karma += contactPoints;
        }
    };

    this.updateMagics = function () {
        if (!this.character.magics || !this.character.magics.spells) {
            return;
        }

        this.magics -= magics.spells.length;
    };

    this.updateMagicSkills = function () {
        const priorities = character.priorities;
        const activeSkills = character.activeSkills;
        if (!priorities || !activeSkills) {
            return;
        }

        if (
            'magic' === priorities.e
            || 'magic' === priorities.d
            || ('magic' === priorities.c && 'magician' === priorities.magic)
            || 'E' === priorities.magicPriority
            || 'D' === priorities.magicPriority
            || ('C' === priorities.magicPriority && 'magician' === priorities.magic)
        ) {
            // No free skills for D and E priorities or C for magician.
            this.magicSkills = 'None';
            return;
        }

        if ('magician' !== priorities.magic) {
            return;
        }

        let free = 2;
        let level;
        if ('magic' === priorities.a || 'A' === priorities.magicPriority) {
            level = 5;
        } else if ('magic' === priorities.b || 'B' === priorities.magicPriority) {
            level = 4;
        }
        $.each(activeSkills, function (unused, skill) {
            if (!free) {
                // All free skills are already spent.
                return;
            }
            if (!skill.magicOnly) {
                // Only magical skills are free.
                return;
            }
            if (skill.level < level) {
                // Only skills higher than the free level count.
                return;
            }
            // Current skill matches our criteria, return the points to the
            // activeSkill pool and decrement our free skills.
            this.activeSkills += level;
            free--;
        });
        this.magicSkills = free + ' at lvl ' + level;
    };

    this.updateResonanceSkills = function () {
        const priorities = character.priorities;
        if (!priorities) {
            return;
        }

        if ('magic' === priorities.e || 'magic' === priorities.d) {
            // Can't be a technomancer lower than C priority.
            this.resonanceSkills = 'None';
            return;
        }

        if ('technomancer' !== priorities.magic) {
            // Priority is high enough, but character is magical.
            this.resonanceSkills = 'None';
            return;
        }

        let free = 3;
        let level;
        const groups = ['tasking', 'electronics', 'cracking'];
        if ('magic' === priorities.c) {
            level = 2;
        } else if ('magic' === priorities.b) {
            level = 4;
        } else {
            level = 5;
        }
        $.each(character.activeSkills, function (unused, skill) {
            if (!free) {
                // All free skills have been spent.
                return
            }
            if (!skill.group) {
                // Skill is not in a group.
                return;
            }
            if (-1 === groups.indexOf(skill.group)) {
                // Skill is not in one of the free groups.
                return;
            }
            if (level > skill.level) {
                // Only skills higher than the level count.
                return;
            }
            // The current skill qualifies, return the free points to the active
            // skills pool.
            this.activeSkills += level;
            free--;
        });
        this.resonanceSkills = free + ' at lvl ' + level;
    };

    this.calculateComplexFormsPoints = function () {
        const forms = this.character.forms;
        if (!forms || {} === forms) {
            return;
        }
        for (let index in forms) {
            this.forms--;
        }
    };

    this.updatePriorities();
    this.updateAttributes();
    this.updateSpecialPoints();
    this.updateKarmaForQualities();
    this.updateKarmaForMartialArts();
    this.updateActiveSkills();
    this.updateSkillGroups();
    this.updateKnowledgeSkills();
    this.updateNuyen();
    this.updateContactPoints();
    this.updateMagics();
    this.updateMagicSkills();
    this.updateResonanceSkills();
    this.calculateComplexFormsPoints();
}
