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

export interface Game {
    id: number;
    poule: Poule;
    team1: TeamResult;
    team2: TeamResult;
    time: string;
}