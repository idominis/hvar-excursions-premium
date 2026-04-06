// Utility Functions
    function normalizeRoleForComparison(role: string): string {
        return role.toLowerCase().replace(/\s+/g, '');
    }

    function getCanonicalRole(inputRole: string, roles: string[]): string {
        const normalizedInput = normalizeRoleForComparison(inputRole);
        return roles.find(role => normalizeRoleForComparison(role) === normalizedInput) || inputRole;
    }
}