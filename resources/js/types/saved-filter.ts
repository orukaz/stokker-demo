export type OrderFilterCriteria = {
    search: string;
    status: string;
    branch: string;
    assignee: string;
    date_from: string;
    date_to: string;
};

export type SavedFilter = {
    id: number;
    view: string;
    name: string;
    filters: OrderFilterCriteria;
    isDefault: boolean;
    updatedAt: string;
};
