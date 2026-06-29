export type SubjectComment = {
    text: string
    createdAt: string
}

export type Subject = {
    name: string
    status: 'active' | 'standby' | 'finished'
    issues: string[]
    comments: SubjectComment[]
    createdAt: string
}
