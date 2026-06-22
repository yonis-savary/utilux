export type PipelineStatus = 'success' | 'failed' | 'running'

export type Pipeline = {
    "id": number,
    "iid": number,
    "project_id": number,
    "sha": string,
    "ref": string,
    "status": PipelineStatus,
    "source": string,
    "created_at": string,
    "updated_at": string,
    "web_url": string,
}


export type Approval = {
    "user_has_approved": boolean,
    "user_can_approve": boolean,
    "approved": boolean,
    "approved_by": [
        {
            "user": {
                "id": number,
                "username": string,
                "public_email": string,
                "name": string,
                "state": string,
                "locked": boolean,
                "avatar_url": string,
                "web_url": string,
            },
            "approved_at": string
        }
    ]
}

export type User =  {
    "id": number,
    "username": string,
    "public_email": string,
    "name": string,
    "state": string,
    "locked": false,
    "avatar_url": string,
    "web_url": string,
}

export type MergeRequest = {
    "id": number,
    "iid": number,
    "project_id": number,
    "title": string,
    "description": string,
    "state": string,
    "created_at": string,
    "updated_at": string,
    "merged_by": unknown,
    "merge_user": unknown,
    "merged_at": unknown,
    "closed_by": unknown,
    "closed_at": unknown,
    "target_branch": string,
    "source_branch": string,
    "user_notes_count": number,
    "upvotes": number,
    "downvotes": number,
    "author": User,
    "assignees": User[],
    "assignee": User,
    "reviewers": User[],
    "source_project_id": number,
    "target_project_id": number,
    "labels": [],
    "draft": boolean,
    "imported": boolean,
    "imported_from": string,
    "work_in_progress": boolean,
    "milestone": unknown,
    "merge_when_pipeline_succeeds": boolean,
    "merge_status": string,
    "detailed_merge_status": string,
    "merge_after": unknown,
    "sha": string,
    "merge_commit_sha": unknown,
    "squash_commit_sha": unknown,
    "discussion_locked": unknown,
    "should_remove_source_branch": unknown,
    "force_remove_source_branch": true,
    "prepared_at": string,
    "reference": string,
    "references": {
        "short": string,
        "relative": string,
        "full": string,
    },
    "web_url": string,
    "time_stats": {
        "time_estimate": number,
        "total_time_spent": number,
        "human_time_estimate": unknown,
        "human_total_time_spent": unknown
    },
    "squash": boolean
    "squash_on_merge": boolean
    "task_completion_status": {
        "count": number,
        "completed_count":number
    },
    "has_conflicts": boolean,
    "blocking_discussions_resolved": boolean
}

export type MergeRequestsResult = {
    mergeRequests: MergeRequest[],
    pipelines: Record<string,Pipeline[]>,
    approvals: Record<string,Approval>
}