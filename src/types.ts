export interface Poule {
    id: number;
    name: string;
}

export interface Team {
    id: number;
    name: string;
}

export interface TeamResult extends Team {
    score: number;
}