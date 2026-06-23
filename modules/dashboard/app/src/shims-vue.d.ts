declare module "*.vue" {
  import type { DefineComponent } from "vue";
  const component: DefineComponent<{}, {}, any>;
  export default component;
}

import type { ConfigAPI } from "./APIs/Config";
import type { JiraAPI } from "./APIs/Jira";
import type { SystemAPI } from "./APIs/System";
import type { GitlabMergeRequestsAPI } from "./APIs/GitlabMergeRequest";
import type { SubjectAPI } from "./APIs/Subject";

declare global {
  interface Window {
    electronAPI: {
      config: typeof ConfigAPI;
      jira: typeof JiraAPI;
      system: typeof SystemAPI;
      gitlabMergeRequests: typeof GitlabMergeRequestsAPI;
      subjects: typeof SubjectAPI;
    };
  }
}