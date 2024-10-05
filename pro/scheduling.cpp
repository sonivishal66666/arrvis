#include <iostream>
#include <vector>
#include <algorithm>
#include <queue>

using namespace std;

// Structure to represent a process
struct Process {
    int pid; // Process ID
    int burst_time; // Burst time
    int arrival_time; // Arrival time
    int priority; // Priority (used for priority scheduling)
};

// Comparison function for sorting by arrival time
bool arrivalTimeSort(const Process &a, const Process &b) {
    return a.arrival_time < b.arrival_time;
}

// FCFS (First Come First Served) Scheduling
void FCFS(vector<Process> &processes) {
    sort(processes.begin(), processes.end(), arrivalTimeSort);
    int current_time = 0;
    cout << "FCFS Scheduling:\n";
    for (auto &p : processes) {
        if (current_time < p.arrival_time) {
            current_time = p.arrival_time;
        }
        cout << "Process " << p.pid << " starts at " << current_time << " and finishes at " << current_time + p.burst_time << "\n";
        current_time += p.burst_time;
    }
}

// SJF (Shortest Job First) Non-preemptive Scheduling
void SJF(vector<Process> &processes) {
    sort(processes.begin(), processes.end(), arrivalTimeSort);
    int current_time = 0;
    vector<Process> ready_queue;
    cout << "SJF Scheduling:\n";
    while (!processes.empty() || !ready_queue.empty()) {
        while (!processes.empty() && processes.front().arrival_time <= current_time) {
            ready_queue.push_back(processes.front());
            processes.erase(processes.begin());
        }
        if (!ready_queue.empty()) {
            auto shortest_job = min_element(ready_queue.begin(), ready_queue.end(), [](Process &a, Process &b) {
                return a.burst_time < b.burst_time;
            });
            Process current_process = *shortest_job;
            cout << "Process " << current_process.pid << " starts at " << current_time << " and finishes at " << current_time + current_process.burst_time << "\n";
            current_time += current_process.burst_time;
            ready_queue.erase(shortest_job);
        } else {
            current_time = processes.front().arrival_time;
        }
    }
}

// Priority Scheduling (Non-preemptive)
void PriorityScheduling(vector<Process> &processes) {
    sort(processes.begin(), processes.end(), arrivalTimeSort);
    int current_time = 0;
    vector<Process> ready_queue;
    cout << "Priority Scheduling:\n";
    while (!processes.empty() || !ready_queue.empty()) {
        while (!processes.empty() && processes.front().arrival_time <= current_time) {
            ready_queue.push_back(processes.front());
            processes.erase(processes.begin());
        }
        if (!ready_queue.empty()) {
            auto highest_priority = min_element(ready_queue.begin(), ready_queue.end(), [](Process &a, Process &b) {
                return a.priority < b.priority;
            });
            Process current_process = *highest_priority;
            cout << "Process " << current_process.pid << " starts at " << current_time << " and finishes at " << current_time + current_process.burst_time << "\n";
            current_time += current_process.burst_time;
            ready_queue.erase(highest_priority);
        } else {
            current_time = processes.front().arrival_time;
        }
    }
}

// Round Robin (RR) Scheduling (Preemptive)
void RoundRobin(vector<Process> &processes, int quantum) {
    queue<Process> ready_queue;
    int current_time = 0;
    cout << "Round Robin Scheduling:\n";
    while (!processes.empty() || !ready_queue.empty()) {
        while (!processes.empty() && processes.front().arrival_time <= current_time) {
            ready_queue.push(processes.front());
            processes.erase(processes.begin());
        }
        if (!ready_queue.empty()) {
            Process current_process = ready_queue.front();
            ready_queue.pop();
            int exec_time = min(quantum, current_process.burst_time);
            cout << "Process " << current_process.pid << " starts at " << current_time << " and runs for " << exec_time << " units\n";
            current_process.burst_time -= exec_time;
            current_time += exec_time;
            if (current_process.burst_time > 0) {
                ready_queue.push(current_process);
            }
        } else {
            current_time = processes.front().arrival_time;
        }
    }
}

// Shortest Remaining Time First (SRTF) Scheduling (Preemptive)
void SRTF(vector<Process> &processes) {
    int current_time = 0;
    vector<Process> ready_queue;
    cout << "SRTF Scheduling:\n";
    while (!processes.empty() || !ready_queue.empty()) {
        while (!processes.empty() && processes.front().arrival_time <= current_time) {
            ready_queue.push_back(processes.front());
            processes.erase(processes.begin());
        }
        if (!ready_queue.empty()) {
            auto shortest_job = min_element(ready_queue.begin(), ready_queue.end(), [](Process &a, Process &b) {
                return a.burst_time < b.burst_time;
            });
            Process &current_process = *shortest_job;
            cout << "Process " << current_process.pid << " starts at " << current_time << "\n";
            current_time++;
            current_process.burst_time--;
            if (current_process.burst_time == 0) {
                ready_queue.erase(shortest_job);
            }
        } else {
            current_time = processes.front().arrival_time;
        }
    }
}

// Main Function to demonstrate the scheduling algorithms
int main() {
    vector<Process> processes = {
        {1, 6, 2, 1},
        {2, 8, 0, 2},
        {3, 7, 1, 3},
        {4, 3, 3, 1}
    };

    vector<Process> processes_copy = processes; // Copy of processes for re-use
    FCFS(processes_copy);
    
    processes_copy = processes;
    SJF(processes_copy);

    processes_copy = processes;
    PriorityScheduling(processes_copy);

    processes_copy = processes;
    RoundRobin(processes_copy, 4);

    processes_copy = processes;
    SRTF(processes_copy);

    return 0;
}
