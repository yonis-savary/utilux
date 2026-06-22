export type Issue = {
    expand: string,
    id: string,
    self: string,
    key: string,
    fields: {
        summary: string,
        parent?: Issue,
        status: {
            self: string,
            description: string,
            iconUrl: string,
            name: string,
            id: string,
            statusCategory: {
                self: string,
                id: number,
                key: string,
                colorName: string,
                name: string,
            }
        },
        priority?: {
            self: string,
            iconUrl: string,
            name: string,
            id: string,
        },
        issuetype?: {
            self: string,
            id: string,
            description: string,
            iconUrl: string,
            name: string,
            subtask: boolean,
            hierarchyLevel: number
        }
    }
}