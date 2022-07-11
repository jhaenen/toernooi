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
    court_num: number;
}

export interface Stats {
    id: number;
    name: string;
    poule_id: number;
    played: number;
    points: number;
    won: number;
    lost: number;
    score_for: number;
    score_against: number;
    score_diff: number;
}