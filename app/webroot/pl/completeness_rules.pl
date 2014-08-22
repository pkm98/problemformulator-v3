% Predicate signifying that an entity is invalid and needs to be annotated
% invalid(id)

% Meta rule 1
% An entity is invalid if it is not connected.
invalid(E) :- entity(E), not connected(E).

% intermediate helper rules

connected(P) :- realizes(_,P).
connected(P) :- realizes(P,_).
connected(P) :- satisfies(_,P).
connected(P) :- satisfies(P,_).
connected(P) :- controls(_,P).
connected(P) :- controls(P,_).
connected(P) :- manages(_,P).
connected(P) :- manages(P,_).
connected(P) :- parameterizes(P,_).
connected(P) :- parameterizes(_,P).
connected(P) :- fulfills(_,P).
connected(P) :- fulfills(P,_).
connected(P) :- is_related_to(_,P).
connected(P) :- is_related_to(P,_).
connected(P) :- decomposition(D,P), decomposes(D,_).

entity(E) :- requirement(E).
entity(E) :- function(E).
entity(E) :- artifact(E).
entity(E) :- behavior(E).
entity(E) :- issue(E).

%--------------------------------

% Meta rule 2
% Every requirement must be satisfied by a function or fulfilled by an artifact
invalid(R) :- requirement(R), not satisfied_or_fulfilled(R).

% intermediate helper rules
satisfied_or_fulfilled(R) :- satisfies(_,R).
satisfied_or_fulfilled(R) :- fulfills(_,R).

%--------------------------------

% Meta rule 3
% Every function must be realized by an artifact
invalid(F) :- function(F), not realized(F).

% intermediate helper rules
realized(F) :- realizes(_,F).

%--------------------------------

#hide.
#show invalid/1.

