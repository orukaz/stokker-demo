export type ProductSummary = {
    id: number;
    title: string;
    link: string | null;
    imageUrl: string | null;
    brand: string | null;
    quantity: number | null;
    price: number | null;
    currency: string;
    isFavorited: boolean;
};

export type InfiniteScrollCollection<T> = {
    data: T[];
};
