# fernandod Journal Agent Notes

## General coding standards

Keep changes narrowly scoped to the requested behavior. Preserve existing behavior unless explicitly asked to change it. Prefer existing local patterns and abstractions. Avoid broad refactors unless the task specifically asks for them.

## Architecture and Maintainability

Follow pragmatic SOLID-style object-oriented design. Keep responsibilities separated and encapsulated behind clear boundaries. Prefer cohesive, reusable classes/components with low coupling, and avoid mixing unrelated concerns such as UI, persistence, business logic, synchronization, and platform-specific code in the same place.

At the same time, avoid over-engineering. Do not create thin pass-through layers, generic interfaces, factories, managers, coordinators, or helper classes unless they solve a real design problem such as multiple implementations, testability, external integration boundaries, or meaningful reuse. Prefer simple, direct, cohesive code over architecture that adds indirection without reducing complexity.
